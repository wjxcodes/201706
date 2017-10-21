<?php
namespace Manage\Controller;
class IndexController extends BaseController  {
    public function index(){
        $pageName = '后台管理中心';
        $this->pageName = $pageName;
        $this->AdminName=$this->getCookieUserName();
        $jumpParam='main';
        if($_GET['pw']){
            $jumpParam='password';
        }
        $this->assign('jumpParam',$jumpParam);
        $this->display();
    }
    //给所有考点加上视频
    public function addKlStudy(){
        $klStudy=$this->getModel('KlStudy');
        $kl=SS('knowledge');
        foreach($kl as $i=>$iKl){
            if($iKl['Last']==1){
                $buffer=$klStudy->selectData('StudyID','KlID='.$i);
                if(empty($buffer)){
                    $klStudy->insertData(array(
                        'Content'=>'',
                        'Careful'=>'',
                        'KlID'=>$i,
                        'Status'=>0,
                        'LoadTime'=>time(),
                        'VideoList'=>'/Uploads/video/2017/0719/0002-0011-0329.mp4#$#'.$iKl['KlName']
                        ));
                }
            }
        }
    }


//    //导入考霸联赛报名用户数据
//    public function insertOnlineUser(){
//        $newpath=realpath('./').'/Public/1.xlsx';
//        $arr=$this->getExcelCommon($newpath);
//
//        $data=array();
//        foreach($arr as $iArr){
//            $data[]=array(
//                'Phonecode'=>$iArr[1],
//                'UserID'=>0,
//                'AddTime'=>time(),
//                'CheckTime'=>0,
//                'PaperType'=>1,
//                'IfWL'=>0,
//                'Status'=>0
//            );
//        }
//        $this->getModel('ExamBuy')->addAllData($data);
//        exit(print_r($arr));
//    }
    //根据知识点查找对应id
    public function getKlToID(){
        $path = iconv('utf-8','gbk//IGNORE',realpath('./').'/Public/');
        $zsdPath = $path.'zsd.txt';
        $buffer = file_get_contents($zsdPath);
        $arr = explode("\r\n",$buffer);
        $knowledge = SS('knowledge');
        $subject = SS('subject');
        $newKl=array();
        foreach($knowledge as $i=>$iKnowledge){
            $newKl[$iKnowledge['KlName']][]='#@'.$i.'#@'.$subject[$iKnowledge['SubjectID']]['SubjectName'];
        }
        $arr=array_filter($arr);
        foreach($arr as $i=>$iArr){
            if($newKl[$iArr]){
                $arr[$i].=implode(';',$newKl[$iArr]);
            }
        }
        $html=implode("\r\n",$arr);
        file_put_contents(iconv('utf-8','gbk//IGNORE',realpath('./').'/Uploads/').'zsdall.txt',$html);
        exit('success');
    }

    //获取知识点数据
    public function getKlByExcel(){
        $path=iconv('utf-8','gbk//IGNORE',realpath('./').'/题组考点索引/题组考点索引/');
        $zsdPath=$path.'zsd.txt';
        $mydir = dir($path);
        while($file = $mydir->read()){
            if((is_dir($path.$file)) || ($file==".") || ($file==".." || ($file=="zsd.txt"))){
                continue;
            }

            $buffer=$this->getExcelCommon($path.$file);
            $str='';
            foreach($buffer as $i=>$iBuffer){
                if($i==0 || empty($iBuffer[2])) continue;
                $str.=$iBuffer[2]."\r\n";
            }

            $html=file_get_contents($zsdPath);
            $html.=$str;
            file_put_contents($zsdPath, $html);
        }
        $mydir->close();
        exit('success');
    }
    //通用获取excel数据
    public function getExcelCommon($newpath){
        //提取excel
        import("Common.Tool.PHPExcel.PHPExcel");
        $filePath=$newpath;//iconv('utf-8','gbk//IGNORE',$newpath);
        $aa      =new \PHPExcel_Reader_Excel2007;
        $bb      =new \PHPExcel_Reader_Excel5;
        if(!$aa->canRead($filePath) && !$bb->canRead($filePath)){
            exit('error|文件无法读取'.$newpath);
        }
        $PHPExcel=\PHPExcel_IOFactory::load($filePath);
        if(!$PHPExcel)
            exit('error|文件读取失败'.$newpath);
        $currentSheet=$PHPExcel->getSheet(0);
        $allColumn   =$currentSheet->getHighestColumn(); // **取得一共有多少列*
        $allRow      =$currentSheet->getHighestRow(); // **取得一共有多少行*

            //按照定义行读取数据
            if($currentSheet->getCell('A2')->getValue() == ''){
                exit('error|上传文档内容有误！');
            }
            $arr   =array();
            $zmList=array();
            $left  ='';
            for($i=0;$i < 4;$i++){
                if($i != 0)
                    $left=chr($i + 64);
                for($j=0;$j < 26;$j++){
                    $zmList[]=$left.chr($j + 65);
                }
            }
            $maxlist=array();
            foreach($zmList as $i=>$iZmList){
                $tmp_str=$currentSheet->getCell($iZmList.'2')->getValue();
                if(empty($tmp_str) || $tmp_str == '')
                    break;
                $maxlist[]=$iZmList;
            }
            if(empty($maxlist)){
                exit('error|上传文档内容有误！');
            }
            for($i=2;$i <= $allRow;$i++){
                foreach($maxlist as $j=>$iZmList){
                    $arr[$i - 2][]=$currentSheet->getCell($iZmList.$i)->getValue();
                }
            }
            return $arr;
    }

    //批量生产试题excel和html
    public function autoTestByKl(){
        $pathKl = iconv('utf-8','gbk//IGNORE',realpath('./').'/Uploads/kl.txt');
        $klbuffer = file_get_contents($pathKl);
        $klbuffer=explode('￥￥￥',$klbuffer);
        $klArray = explode("\r\n",$klbuffer[1]);

        $tmpBuffer=array();
        foreach($klArray as $i=>$iKlArray){
            $tmp=explode('#@',$iKlArray);
            if($tmp[1]) $tmpBuffer[$tmp[1]]=$tmp;
        }
        $klArray=$tmpBuffer;

        foreach($klArray as $i=>$iKlArray){
            echo $iKlArray[1].'---'.$iKlArray[0].'---'.$iKlArray[2].'<br/>';
            $id=$iKlArray[1];
            if($iKlArray[3]) $id.=','.$iKlArray[3];
            if($iKlArray[5]) $id.=','.$iKlArray[5];
            $this->testExcel($id,$iKlArray[0]);
        }
    }

    //生成试题excel和html
    public function testExcel($klid,$klname){
        set_time_limit(0);
        $rootpath=realPath('./').'/Uploads/testexcel/';
        if(!is_dir($rootpath)){
            mkdir($rootpath,0755,true);
        }
        $testidpath=$rootpath.'testid.txt'; //存储试题id防止取出重复id


//        $klnames=iconv('utf-8','gbk//IGNORE',$klname);
        $klnames=$klname;
        $klpath=$rootpath.$klnames.'.xls';

        if(!is_dir($rootpath.'testhtml/'.$klnames)){
            mkdir($rootpath.'testhtml/'.$klnames,0755,true);
        }
        $where['KlID']=$klid;
        $where['ShowWhere']=array(0,1);
        $where['Duplicate']='0';
        $where['DocTypeID']=array(1,2,3,5);
        $where['DiffNum']=array(0.45,0.65);
        $where['LastTime']=time()-365*2*24*3600;


        $testidcon=file_get_contents($testidpath);
        $testid=str_replace("\r\n",',',$testidcon);
        $testid=explode(",",$testid);
        $testid=array_filter($testid);
        if($testid){
            $where['testid']=array($testid);
            $where['testfilter']=1;
        }

        $order=array('@random');
        $field=array('testid','test','answer','analytic','typesid','typesname','ifchoose','diff','subjectname','klnameonly');
        $page=array('page'=>1,'perpage'=>50,'limit'=>50);

        $tmpStr=R('Common/TestLayer/indexTestList',array($field,$where,$order,$page));
        if($tmpStr === false){
             $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        if($tmpStr[1]<40){
            unset($where['LastTime']);
            $tmpStr=R('Common/TestLayer/indexTestList',array($field,$where,$order,$page));
        }
        //输出试题属性看试题是否正常
        echo $klid.'---'.$klnames.'---'.$tmpStr[1].'<br/>';

        $testid=array();
        $typesArray=array();//按照题型排序
        $imgArray=array(); //图像数组
        foreach($tmpStr[0] as $i=>$iTmpStr){
            $testid[]=$tmpStr[0][$i]['testid']; //防止重复

            $ii=1;
            $tmpStr[0][$i]['klnameonly']=str_replace('<br/>',"\r\n",$tmpStr[0][$i]['klnameonly']);

            //处理图片
            $host=C('WLN_DOC_HOST');
            $hostin=C('WLN_DOC_HOST_IN');

            //提取图片
            $tt=$tmpStr[0][$i]['test'].$tmpStr[0][$i]['answer'].$tmpStr[0][$i]['analytic'];
            preg_match_all('/<img[\s]*(style=[\"|\'][^\"\']*[\"|\'])?[\s]*src=[\'|\"]([^\"\']*)[\"|\']/is',$tt,$arr);
            foreach($arr[2] as $arrn){
                $imgArray[]=(str_replace($host,$hostin,$arrn));
            }

            $tmpStr[0][$i]['test']=str_replace($host,'../..',$tmpStr[0][$i]['test']);
            $tmpStr[0][$i]['answer']=str_replace($host,'../..',$tmpStr[0][$i]['answer']);
            $tmpStr[0][$i]['analytic']=str_replace($host,'../..',$tmpStr[0][$i]['analytic']);
            if($tmpStr[0][$i]['ifchoose']>1) $ii=0;
            $tmpStr[0][$i]['answer']=R('Common/TestLayer/delMoreTag',array($tmpStr[0][$i]['answer'],$ii));

            $typesArray[$tmpStr[0][$i]['typesid']][]=$tmpStr[0][$i];

        }
        ksort($typesArray);

        $k=0;
        $arr=array();
        foreach ($typesArray as $i=> $iTypesArray) {
            foreach ($iTypesArray as $j=> $jTypesArray) {
                $k=$k+1;
                $htmlpath='testhtml/'.$klname.'/'.$k.'.html';
                $arr[] = [$k,$jTypesArray['test'],$jTypesArray['answer'],$jTypesArray['analytic'],$htmlpath.'{#urlTag#}'.iconv('utf-8','gbk//IGNORE',$htmlpath),$jTypesArray['diffname'],$jTypesArray['typesname'],$jTypesArray['klnameonly'],'',''];

                //生成html
                $htmlpath=$rootpath.$htmlpath;
                $test=$jTypesArray['test'];
                file_put_contents(str_replace($k.'.html',$k.'_test.html',$htmlpath),iconv('utf-8','gbk//IGNORE',$test));
                $answer='<p>【答案】</p>'.$jTypesArray['answer'].'<p>【解析】</p>'.$jTypesArray['analytic'];
                file_put_contents(str_replace($k.'.html',$k.'_answer.html',$htmlpath),iconv('utf-8','gbk//IGNORE',$answer));

                $html=$test.$answer;
                file_put_contents($htmlpath,iconv('utf-8','gbk',$html));
            }
        }

        //下载图片
        $imgArray=array_unique($imgArray);
        if($imgArray){
            foreach($imgArray as $iImgArray){
                $imgcon=file_get_contents($iImgArray);
                $newpath=$rootpath.str_replace($hostin.'/','',$iImgArray);
                if(is_file($newpath)) continue;
                if(!is_dir(dirname($newpath))) mkdir(dirname($newpath),0755,true);
                file_put_contents($newpath,$imgcon);
            }
        }

        //记录试题id
        if($testidcon) $testidcon=$testidcon."\r\n".implode(',',$testid);
        else $testidcon=implode(',',$testid);
        file_put_contents($testidpath,$testidcon);

        //生成excel
        $keyName=array('ID','题文','答案','解析','链接','难度','题型','考点','技能','能力');
        $keyWidth=array('10','30','30','30','15','10','10','20','10','10');
        $excelName=array('title'=>'试题列表','excelName'=>'试题信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$arr,$excelName,$klpath));
        echo ('success');
    }
    /**
    public function editUser(){
        $userID=$_GET['UserID'];
        $field=$_GET['field'];
        $s=$_GET['s'];
        $user=$this->getModel('User');
        $where='UserID='.$userID;
        if($s==md5($userID.$field.date('Ymd'))){
            if($field=='Phonecode'){
                $user->updateData(array('Phonecode'=>''),$where);
            }elseif($field=='Email'){
                $user->updateData(array('Email'=>''),$where);
            }elseif($field=='delete'){
                $user->deleteData($where);
            }
            exit();
        }
        exit($userID.'no access');
    }**/
    /**
     * 处理用户名手机邮箱历史遗留问题
     */
    /*
    public function checkUserNameAndPhone1(){
        header('Content-type:text/html; charset=utf8');
        $user=$this->getModel('User');*/
/*
        //如果手机号字段不是11位则变为空
        $buffer=$user->selectData('UserID,UserName,Phonecode,Email','1=1','UserID asc');
        foreach($buffer as $iBuffer){
            if($iBuffer['Phonecode']=='') continue;
            if(!is_numeric($iBuffer['Phonecode']) || strlen($iBuffer['Phonecode'])!=11){
                if(strlen(trim($iBuffer['Phonecode']))==11){
                    //更新数据消除空格
                    $user->updateData(array('Phonecode'=>trim($iBuffer['Phonecode'])),array('UserID'=>$iBuffer['UserID']));
                }else{
                    $user->updateData(array('Phonecode'=>''),array('UserID'=>$iBuffer['UserID']));
                }
            }

            //如果邮箱号字段不带@则变为空
            if($iBuffer['Email']!='' and !strstr($iBuffer['Email'],'@')){
                $user->updateData(array('Email'=>''),array('UserID'=>$iBuffer['UserID']));
            }

        }
exit();*/
/*
        //找到重复的用户对没有验证的用户同时账号密码不一致的赋值为空
        $buffer=$this->checkUserNameAndPhone();

        foreach($buffer as $iBuffer){
            if($iBuffer!=''){
                if(strstr($iBuffer,'@')){
                    $thisData=$user->selectData('UserID,UserName,Phonecode,Email,CheckPhone,CheckEmail','UserName="'.$iBuffer.'" or Email="'.$iBuffer.'"','UserID asc');
                }else{
                    $thisData=$user->selectData('UserID,UserName,Phonecode,Email,CheckPhone,CheckEmail','UserName="'.$iBuffer.'" or Phonecode="'.$iBuffer.'"','UserID asc');
                }
                $tjP=0;
                $tjE=0;
                foreach($thisData as $kThisData){
                    if($kThisData['CheckPhone']==1){
                        $tjP++;
                    }
                    if($kThisData['CheckEmail']==1){
                        $tjE++;
                    }
                }
                if($tjP>1 || $tjE>1) continue;

                    $output=array('Phonecode'=>'修改手机号为空',
                                  'Email'=>'修改邮箱为空',
                                  'delete'=>'删除账号',
                    );
                $str='';
                foreach($thisData as $k=>$kThisData){
                    foreach($output as $h=>$hOutput){
                        $s=md5($kThisData['UserID'].$h.date('Ymd'));
                        $str.='<a href="/Manage-Index-editUser-UserID-'.$kThisData['UserID'].'-field-'.$h.'-s-'.$s.'" target="_blank">'.$hOutput.$k.'</a><br/>';
                    }
                }
                echo $str;
                exit(print_r($thisData));
            }
        }


    }*/
    /**
     * 处理用户名手机邮箱历史遗留问题
     *//*
    public function checkUserNameAndPhone(){
        $user=$this->getModel('User');
        $buffer=$user->selectData('UserID,UserName,Phonecode,Email','1=1','UserID asc');

        $output=array();
        foreach($buffer as $iBuffer){
            $userID=$iBuffer['UserID'];
            $userName=$iBuffer['UserName'];
            $email=$iBuffer['Email'];
            $phonecode=$iBuffer['Phonecode'];

            $flag=0;
            //找到重复的账号
            if($userName==$email){
                if(empty($output[$userName])) $output[$userName]=1;
                else $output[$userName]++;
                $flag=1;
            }else{
                if(empty($output[$email])) $output[$email]=1;
                else $output[$email]++;
            }

            if($userName==$phonecode){
                if(empty($output[$userName])) $output[$userName]=1;
                else $output[$userName]++;
                $flag=1;
            }else{
                if(empty($output[$phonecode])) $output[$phonecode]=1;
                else $output[$phonecode]++;
            }

            if($flag==0){
                if(empty($output[$userName])) $output[$userName]=1;
                else $output[$userName]++;
            }
        }

        foreach($output as $i=>$iOutput){
            if($iOutput>1){
                $result[]=$i;
            }
        }
        return $result;
    }*/
    /**
     * 检查用户
     *//*
    public function checkUser(){
        $user=$this->getModel('User')->selectData('User','UserID,LoadDate','1=1','UserID asc');
        $tmp=0;
        $output=array();
        foreach($user as $iUser){
            if($tmp==0) $tmp=$iUser['UserID'];
            else if($tmp==$iUser['UserID']-1){
                $tmp=$iUser['UserID'];
            }else{
                for($j=$tmp+1;$j<$iUser['UserID'];$j++){
                    if($iUser['UserID']>1000) $output[]=array('UserID'=>$j,'LoadDate'=>$iUser['LoadDate']);
                }
                $tmp=$iUser['UserID'];
            }
        }
        exit(print_r($output));
    }
    public function insertUserFor($taskID,$taskNum,$start){
        //加入数据到任务大厅
        $usertmp=M('Usertmp');
        $bufferTmp=$usertmp->order('UserID asc')->limit($start.','.$taskNum)->select();


        $missionHallTasks=M('MissionHallTasks');
        $missionHallRecords=M('MissionHallRecords');
        $user=M('User');
        $buffer=$missionHallTasks->where('MHTID='.$taskID)->select();
        $limit=$buffer[0]['Limit'];
        $num=$buffer[0]['Num'];

        foreach($bufferTmp as $a){
            $userBuffer=$user->field('UserID,LoadDate')->where('UserName="'.$a['UserName'].'"')->select();
            if($userBuffer==false) continue;


            $data=array();
            $data['Num']=$num+1;
            $num++;
            $missionHallTasks->where('MHTID='.$taskID)->data($data)->save();

            $data=array();
            $data['MHTID']=$taskID;
            $data['UserID']=$userBuffer[0]['UserID'];
            $data['Status']=$limit;
            $data['AddTime']=$userBuffer[0]['LoadDate']+rand(600,120*60);
            $missionHallRecords->data($data)->add();
        }
    }*/
    /**
     * 测试app
     *//*
    public function insertUser(){
        $missionHallRecords=M('MissionHallRecords');
        $buffer=$missionHallRecords->where('AddTime<'.strtotime('2015-11-05 13:00:00'))->select();

        foreach($buffer as $iBuffer){

            $missionHallRecords->data(array('AddTime'=>rand(strtotime('2015-11-11 13:00:00'),strtotime('2015-11-11 21:00:00'))))->where('MHRID='.$iBuffer['MHRID'])->save();
        }

        exit();

        $start=0;

        $tmpArr=array(
            array('taskID'=>66,'taskNum'=>1000),
            array('taskID'=>68,'taskNum'=>1000)
        );
//        ,
//        array('taskID'=>80,'taskNum'=>75),
//            array('taskID'=>79,'taskNum'=>156),
//            array('taskID'=>78,'taskNum'=>102),
//            array('taskID'=>74,'taskNum'=>72),
//            array('taskID'=>73,'taskNum'=>72),
//            array('taskID'=>72,'taskNum'=>70),
//            array('taskID'=>77,'taskNum'=>77),
//            array('taskID'=>134,'taskNum'=>76),
//            array('taskID'=>142,'taskNum'=>75)
        foreach($tmpArr as $tmpArrn){
            $this->insertUserFor($tmpArrn['taskID'],$tmpArrn['taskNum'],$start);
            $start+=$tmpArrn['taskNum'];
        }

        exit();

        $usertmp=M('Usertmp');
        $bufferTmp=$usertmp->order('UserID asc')->select();
            $user=$this->getModel('User');
            $user111=M('User');

        $times=floor(12*60*60/2500);
        $start=strtotime('2015-11-11 08:30:00');
        $LoadDate=$start;

        foreach($bufferTmp as $a){

            //检测用户名是否存在
            $ifPhone=$user111->field('UserID')->where('UserName="'.$a['UserName'].'" or Phonecode="'.$a['UserName'].'"')->select();

            if($ifPhone!=false){

                echo $ifPhone[0]['UserID'].'<br>';
                continue;
            }

            $ip=$user111->field('LastIP')->order('RAND()')->limit(1)->select();
            $ip=$ip[0]['LastIP'];

            $LoadDate+=rand($times/2,$times);

            $username=$a['UserName'];
            $password='11111111';
            $RealName=$a['RealName'];

            //检查是否使用用户邮箱
            $ifEmail=$user111->field('UserID')->where('UserName="'.$a['Email'].'" or Email="'.$a['Email'].'"')->select();
            $Email='';
            if(!$ifEmail){
                $Email=$a['Email'];
            }

            $SchoolName=$a['SchoolName'];

            //获取编号
            $autoInc=$this->getModel('AutoInc');
            $orderNum=$autoInc->getOrderNum();

            $data=array();
            $data['UserName']=$username;
            $data['Password']=md5($username.$password);
            $data['RealName']=$RealName;
            $data['Sex']=0;
            $data['Phonecode']=is_numeric($username) ? $username : '';
            $data['Email']=$Email;
            $data['Address']=$SchoolName;
            $data['PostCode']='';
            $data['LoadDate']=$LoadDate;
            $data['LastTime']=$LoadDate;
            $data['Logins']=0;
            $data['Whois']=1;
            $data['LastIP']=$ip;
            $data['CheckPhone']=0; //已验证手机号
            $data['SaveCode']=$user->saveCode();
            $data['OrderNum']=$orderNum;
            $data['IfShowTime']=1;

            $result = $this->getModel('User')->insertData($data);
            if($result){
                $data['UserID']=$result;
                //注册时添加指定分组  2015-9-2
                $this->getModel('UserGroup')->addDefaultGroupAtRegistration($result, $ipUser);
            }
        }

    }*/
//    /**
//     * 测试app
//     */
//    public function testApp(){
//        $appMsg=R('Common/AppPlatformLayer/getStatistic',[$platform=['android'],$start=time(),$end=time()]);
//        exit(print_r($appMsg));
//        if($appMsg){
//            $appDownNum=$appMsg['android']['devicesCount'];           //设备安装次数
//        }else{
//            $appDownNum=7394;         //没有获取到的话，就默认7394次；
//        }
//        return $appDownNum;
//    }
    /**
     * 测试索引
     */
    /*public function testIndexCustom(){
        $file_uri = (DIRECTORY_SEPARATOR == '\\') ? '/coreseek/api/sphinxapi.php' : 'coreseek/api/sphinxapi.php';
        require(__ROOT__.$file_uri);
        $index = 'zjcustom,delta_zjcustom'; //索引名称
        //$index = 'zjtest,delta_zjtest'; //索引名称
        $cl    = new SphinxClient();
        $cl->SetServer(C('WLN_INDEX_HOST'), 9312);
        $cl->SetConnectTimeout(3);
        $cl->SetArrayResult(true);
        $cl->SetMatchMode ( SPH_MATCH_EXTENDED2 );
        //$cl->setFilter ( 'status',array(0) );

        //$xxx=$cl->UpdateAttributes($index,array('status'),array(11=>array(-1)));
        $res=$cl->Query('', $index);
        exit(print_r($res).'aa'.$xxx.'aa');
    }*/
    /**
     * 修复班级内学生数量数据
     */
   /* public function updateClassListCount(){
        $buffer=$this->dbConn->selectData(
            'ClassList',
            '*',
            '1=1',
            'ClassID ASC'
        );
        foreach($buffer as $iBuffer){
            $userlist=$this->dbConn->selectData(
                'ClassUser',
                '*',
                'ClassID='.$iBuffer['ClassID'].' and SubjectID=0',
                'UserID ASC'
            );

            //判断是否有教师账户被当做学生
            $userIDArr=array();
            foreach($userlist as $j=>$jUserList){
                $userIDArr[]=$jUserList['UserID'];
            }
            $userlist2=$this->dbConn->selectData(
                'User',
                '*',
                'UserID in ('.implode(',',$userIDArr).')',
                'UserID ASC'
            );
            $userIDDel=$userIDArr;
            if($userlist2){
                foreach($userlist2 as $j=>$jUserList2){
                    if(in_array($jUserList2['UserID'],$userIDArr)){
                        unset($userIDDel[array_search($jUserList2['UserID'],$userIDArr)]);
                    }
                }
            }
            if($userIDDel) $this->getModel('ClassUser')->deleteData('UserID in ('.implode(',',$userIDDel).')');


            $tmp=$this->dbConn->selectCount(
                'ClassUser',
                'ClassID='.$iBuffer['ClassID'].' and SubjectID=0',
                'CUID'
            );
            $this->getModel('ClassList')->updateData(
                array('StudentCount'=>$tmp),
                'ClassID='.$iBuffer['ClassID']
            );
        }
    }*/

    /**
     * 修复反馈数据的新增字段OpenStyle初始值
     */
   /* public function updateFeedBack(){
        $result=$this->dbConn->selectData('FeedBack','*','Style=0','FeedBackID DESC');

        $ipArr=array();
        $excelArr=array();
        $personArr=array();
        foreach ($result as $i => $value) {
            $newArray=preg_split('/<br[^>]*>/i',$value['Content']); //分割content，获取相关信息
            foreach($newArray as $j=>$jNew){
                if(strpos($newArray[$j],'P：')){
                    $ipArr[]=$value['FeedbackID'];
                }
            }
            if($value['FilePath']){
                $excelArr[]=$value['FeedbackID'];
            }
            if(!strpos($newArray[$j],'P：') && empty($value['FilePath'])){
                $personArr[]=$value['FeedbackID'];
            }
        }
        $feedback=$this->getModel('FeedBack');
        $ipStr=implode(',',$ipArr);
        $ipRes=$feedback->updateData(
            'OpenStyle=ip',
            'FeedbackID in ('.$ipStr.')'
        );
        $excelStr=implode(',',$excelArr);
        $excelRes=$feedback->updateData(
            'OpenStyle=name',
            'FeedbackID in ('.$excelStr.')'
        );
        $personalStr=implode(',',$personArr);
        $personalRes=$feedback->updateData(
            'OpenStyle=personal',
            'FeedbackID in ('.$personalStr.')'
        );
        if($ipRes && $excelRes && $personalRes){
            $this->showSuccess('数据更新成功','__URL__');
        }
    }*/
    /**
     * 拷贝当前user表的Times字段数据到ComTimes中
     * 网上更新数据后 请将方法访问权限更改为private 或删除该方法
     * @author demo
     */
    /*public function copyUserTimes(){
        //if(get_client_ip(0,true)!='1.192.121.104')header('Location:/');//ip限制
        $result=$this->dbConn->execute('update zj_user set ComTimes=Times');
        if($result===0){
            echo '数据已经更新过了';
        }elseif($result>0){
            echo '更新了'.$result.'条数据';
        }else{
            $this->dbConn->addSqlErrorLog($result);
        }
    }*/
    /**
     * 拷贝当前user表的Times字段数据到ComTimes中
     * 网上更新数据后 请将方法访问权限更改为private 或删除该方法
     * @author demo
     */
    /*public function calcComTimes(){
        $user=$this->getModel('User');
        $docdown=$this->getModel('Docdown');
        $buffer=$user->order('UserID ASC')->select();
        foreach($buffer as $iBuffer){
            $count=$docdown->where('UserName="'.$iBuffer['UserName'].'"')->count('DownID');
            if($iBuffer['ComTimes']<$count){
                $user->where('UserName="'.$iBuffer['UserName'].'"')->save(array('ComTimes'=>$count));
                echo $count.'<br>';
            }
        }
    }*/
    /*改test表和testAttr表结构 移动相应数据到对应表*/
    /*public function moveAttrData(){
        //修改时一次执行不完，分多次执行
        $test=M('Test');
        $testAttr=M('TestAttr');
        $buffer=$test->field('TestID,NumbID,Status,IfWL,FirstLoadTime')->order('TestID DESC')->select();
        if($buffer){
            foreach($buffer as $iBuffer){
                $data=array();
                $data['NumbID']=$iBuffer['NumbID'];
                $data['Status']=$iBuffer['Status'];
                $data['IfWL']=$iBuffer['IfWL'];
                $data['FirstLoadTime']=$iBuffer['FirstLoadTime'];
                $testAttr->data($data)->where('TestID='.$iBuffer['TestID'])->save();
            }
        }
        $test=M('TestReal');
        $testAttr=M('TestAttrReal');
        $buffer=$test->field('TestID,NumbID,Status,IfWL,FirstLoadTime,LoadTime')->order('TestID DESC')->limit(0,50001)->select();
        if($buffer){
            foreach($buffer as $iBuffer){
                $data=array();
                $data['NumbID']=$iBuffer['NumbID'];
                $data['Status']=$iBuffer['Status'];
                $data['IfWL']=$iBuffer['IfWL'];
                $data['FirstLoadTime']=$iBuffer['FirstLoadTime'];
                $data['LoadTime']=$iBuffer['LoadTime'];
                $testAttr->data($data)->where('TestID='.$iBuffer['TestID'])->save();
            }
        }
    }*/
    /*知识点学习 重难点+数字去掉*/
    /*public function delKlStudyStr(){
        $klStudy=M('KlStudy');
        $buffer=$klStudy->where('VideoList like "%重难点%"')->select();
        foreach($buffer as $iBuffer){
            $data=array();
            $data['StudyID']=$iBuffer['StudyID'];
            $data['VideoList']=preg_replace('/重难点[0-9]+/i','',$iBuffer['VideoList']);
            $klStudy->data($data)->save();
        }
    }*/
    /*匹配答案是全角字母的数据并输出试题id和答案 重新匹配试题类型等信息*/
    /*public function resetTestStyle(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $list=array('Ａ','Ｂ','Ｃ','Ｄ');
        $subject=S('subject');
        $testdoc=$this->getModel('Testdoc');
        $test=$this->getModel('TestReal');
        $testAttr=$this->getModel('TestAttrReal');
        $buffer=$test->
            field('t.TestID,t.Test,t.Answer,a.SubjectID')->
            table('zj_test_real t')->
            join('zj_test_attr_real a on t.TestID=a.TestID')->
            where('t.Answer like "%Ａ%"')->
            order('a.SubjectID asc,t.TestID asc')->
            select();
        //$buffer=$test->
        //    field('t.TestID,t.Test,t.Answer,a.SubjectID')->
        //    table('zj_test_real t')->
        //    join('zj_test_attr_real a on t.TestID=a.TestID')->
        //    where('t.TestID=195459')->
        //    order('a.SubjectID asc,t.TestID asc')->
        //    select();
        foreach($buffer as $i=>$iBuffer){
                $tmp=preg_replace('/<[^>]*>|\s|　| /i','',$iBuffer['Answer']);
                if(!in_array($tmp,$list)){
                    continue;
                }
                echo '学科:'.$subject[$iBuffer['SubjectID']]['SubjectName'].' ---试题id：'.$iBuffer['TestID'].'<br>';
                //修改答案
                $thisBuffer=$testdoc->field('DocAnswer')->where('TestID ='.$iBuffer['TestID'])->select();
                $docAnswer=str_replace($list,array('A','B','C','D'),$thisBuffer[0]['DocAnswer']);
                $testdoc->data(array('TestID'=>$iBuffer['TestID'],'DocAnswer'=>$docAnswer))->save();
                $docAnswer=str_replace($list,array('A','B','C','D'),$iBuffer['Answer']);
                $test->data(array('TestID'=>$iBuffer['TestID'],'LoadTime'=>time(),'Answer'=>$docAnswer))->save();

                $tmpArr=array();
                $tmpArr['Test']=$iBuffer['Test'];
                $tmpArr['Answer']=$docAnswer;
                $ifChoose=$test->judgeChoose($tmpArr);
                $testStyle=1;
                if($ifChoose==0){
                    $testStyle=3;
                    $optionWidth=0;
                    $optionNum=0;
                }else{
                    $strInfo =$test->getOptionWidth($iBuffer['Test']);
                    if($strInfo[0]){
                        $optionNum=$strInfo[0][0];
                        $optionWidth=$strInfo[0][1];
                    }else{
                        $optionNum=0;
                        $optionWidth=0;
                    }
                }
                $data=array();
                $data['TestID'] = $iBuffer['TestID'];
                $data['IfChoose'] = $ifChoose;
                $data['TestStyle'] = $testStyle;
                $data['OptionWidth'] = $optionWidth;
                $data['OptionNum'] = $optionNum;
                $testAttr->data($data)->save();
            }
    }*/
    /*获取学校列表 显示完整地区*/
    /*public function getSchoolList(){
        $school=$this->getModel('School');
        $areaParentList=SS('areaParentPath');
        $areaList=SS('areaList');
        $buffer=$school->where('`type`=1 and `Status`=2')->order('AreaID asc')->select();
        $output=array();
        foreach($buffer as $i=>$iBuffer){
            $output[$i]['SchoolName']=$iBuffer['SchoolName'];
            $areaName=array();
            if($areaParentList[$iBuffer['AreaID']]){
                foreach($areaParentList[$iBuffer['AreaID']] as $jAreaParentList){
                    $areaName[]=$jAreaParentList['AreaName'];
                }
            }
            $areaName[]=$areaList[$iBuffer['AreaID']];
            $output[$i]['AreaName']=implode(' >> ',$areaName);
            echo $output[$i]['AreaName'].'--'.$output[$i]['SchoolName'].'<br/>';

        }

    }*/
    /*为新增学科id匹配数据*/
    /*public function setSendWorkSubjectID(){
        $model = $this->getModel('UserSendWork');
        $all = $model->field('SendID,zj_user_work.SubjectID')
            ->join('zj_user_work ON zj_user_send_work.WorkID=zj_user_work.WorkID')
            ->select();
        foreach($all as $k){
            $data = ['SendID'=>$k['SendID'],'SubjectID'=>$k['SubjectID']];
            if($model->save($data)){
                $ok = 'ok';
            }else{
                $ok = 'error!!!!!!!!!!!!!!!!!!!!';
            }
            echo $k['SendID'].'--'.$k['SubjectID'].'--'.$ok.'<br />';
        }
    }*/
    /*
    public function changeGroup(){
        //旧User表 GroupID权限组id, EndTime到期时间, IfTeacher标引教师, Ifcherker审核教师 ,IfEq公式处理,Whois=1教师用户
        $user = $this->getModel('User');
        //新表 UGID, GroupID权限组id ,UserID用户id, GroupName ,所属组编号组卷1,提分2,教师3,                  UserGroupName(字段数据库文档没看到,如果没下面删除$data['UserGroupName']) ,AddTime添加时间(user表无添加时间), LastTime到期时间
        $usergroup = $this->getModel('UserGroup');
        //查user表 条件1.教师用户 Whois=1,2.有权限分组
        $list = $user->selectData('UserID,GroupID,IfTeacher,IfChecker,EndTime,Whois');
        $resault = 0;
        foreach($list as $i=>$val){
            if($val['Whois']==1){//教师用户
                //看user_group表中有无当前用户数据(注：查询条件需根据UserGroup数据改动),如果没,就插入数据
                $buffer = $usergroup->selectData('UGID','GroupName = 1 AND UserID = "'.$val['UserID'].'"');
                if(!$buffer){
                    $data['GroupName'] = 1;
                    $data['AddTime'] = time();
                    $data['LastTime'] = $val['EndTime'];
                    $data['UserID'] = $val['UserID'];
                    $data['GroupID'] = $val['GroupID'];
                    $res = $usergroup->insertData($data);
                    if($res){$resault = 1;}else{exit($i);}
                    //标引教师
                    if($val['IfTeacher']){
                        $data['GroupName'] = 3;
                        $data['GroupID'] = 33;// 注：需根据power_user表里对应的PUID定
                        $res = $usergroup->insertData($data);
                        if($res){$resault = 1;}else{exit($i);}
                    }
                    //审核教师
                    if($val['IfChecker']){
                        $data['GroupName'] = 3;
                        $data['GroupID'] = 32;// 注：需根据power_user表里对应的PUID定
                        $res = $usergroup->insertData($data);
                        if($res){$resault = 1;}else{exit($i);}
                    }
                    //公式处理
                    if($val['IfEq']){
                        $data['GroupName'] = 3;
                        $data['GroupID'] = 34;// 注：需根据power_user表里对应的PUID定
                        $res = $usergroup->insertData($data);
                        if($res){$resault = 1;}else{exit($i);}
                    }
                }
            }else if($val['Whois']==0){//学生用户
                $buffer1 = $usergroup->selectData('UGID','GroupName = 2 AND UserID = "'.$val['UserID'].'"');
                if(!$buffer1){
                    $data['AddTime'] = time();
                    $data['GroupName'] = 2;
                    $data['LastTime'] = $val['EndTime'];
                    $data['UserID'] = $val['UserID'];
                    $data['GroupID'] = $val['GroupID'];
                    $res = $usergroup->insertData($data);
                    if($res){$resault = 1;}else{exit('提分'.$i);}
                }
            }
        }
        if($resault){
            exit('success');
        }else{
            exit('false');
        }
    }*/
    //过滤章节不匹配数据
    /*public function fliterChapter(){
        set_time_limit(0);
        $doc=M('Doc');
        $testAttrReal=M('TestAttrReal');
        $testChapterReal=M('TestChapterReal');
        $chapter=$this->getModel('Chapter');
        $parpage=2;
        $start=strtotime('2014-06-03');
        $end=strtotime('2014-09-01');
        $count=$doc->where('IntroFirstTime between '.$start.' and '.$end)->count('DocID');

        for($i=0;$i<$count;$i=$i+$parpage){
            $buffer=$doc->field('DocID')->where('IntroFirstTime between '.$start.' and '.$end)->order('DocID asc')->limit($i.','.$parpage)->select();
            //找到试题
            $idList=array();
            foreach($buffer as $jBuffer){
                $idList[]=$jBuffer['DocID'];
            }
            $buffer=$testAttrReal->field('TestID')->where('DocID in ('.implode(',',$idList).')')->select();
            //找到试题的章节
            $idList=array();
            foreach($buffer as $jBuffer){
                $idList[]=$jBuffer['TestID'];
            }
            $buffer=$testChapterReal->where('TestID in ('.implode(',',$idList).')')->select();
            $idList=array();
            foreach($buffer as $jBuffer){
                if($jBuffer['ChapterID']){
                    $idList[$jBuffer['TestID']][]=$jBuffer['ChapterID'];
                }
            }
            //过滤章节
            foreach($idList as $j=>$jIdList){
                $tmp=array();
                $tmp=$chapter->filterChapterID($jIdList);
                $tmp2=array();
                $tmp3=array();
                foreach($tmp as $jTmp){
                    $tmp2[$jTmp]=$jTmp;
                }
                foreach($jIdList as $kIdList){
                    if(!$tmp2[$kIdList]) {
                        $tmp3[]=$kIdList;
                    }
                }
                //删除不必要的章节
                if($tmp3){
                    echo $j.'-=-=-'.implode(',',$tmp3).'<br/>';
                    //$testChapterReal->where('TestID='.$j.' and ChapterID in ('.implode(',',$tmp3).')')->delete();
                }
            }
        }
    }*/
    //英语章节匹配
    /*public function setChapterEng(){
        $testAttrReal=M('TestAttrReal');
        $testChapterReal=M('TestChapterReal');
        $testReal=M('TestReal');
        $testKlReal=M('TestKlReal');
        $chapterKey=$this->getModel('ChapterKey');
        $chapterKl=$this->getModel('ChapterKl');
        $chapter=$this->getModel('Chapter');
        $SubjectID=14;
        $count=$testAttrReal->where('SubjectID='.$SubjectID)->count('TestID');
        $parpage=500;
        for($i=0;$i<$count;$i=$i+$parpage){
            $buffer=$testAttrReal->field('TestID')->where('SubjectID='.$SubjectID)->order('TestID asc')->limit($i.','.$parpage)->select();
            $idList=array();
            $idList2=array();
            if($buffer){
                foreach($buffer as $j=>$iBuffer){
                    $idList[]=$iBuffer['TestID'];
                    $idList2[$iBuffer['TestID']]=$iBuffer['TestID'];
                }
                echo $i.'-=-=-'.implode(',',$idList).'<br/>';
                $buffer=$testChapterReal->where('TestID in ('.implode(',',$idList).')')->select();
                if($buffer){
                    foreach($buffer as $j=>$iBuffer){
                        if($iBuffer['ChapterID']!=0)
                        unset($idList2[$iBuffer['TestID']]);
                    }
                }

                //获取知识点和关键字对应章节
                if($idList2){
                    $buffer=$testKlReal->where('TestID in ('.implode(',',$idList2).')')->select();
                    $klList=array();
                    if($buffer){
                        foreach($buffer as $j=>$iBuffer){
                            $klList[$iBuffer['TestID']][]=$iBuffer['KlID'];
                        }
                    }
                    $buffer=$testReal->field('TestID,Analytic')->where('TestID in ('.implode(',',$idList2).')')->select();
                    $keyList=array();
                    if($buffer){
                        foreach($buffer as $j=>$iBuffer){
                            $keyList[$iBuffer['TestID']]=$iBuffer['Analytic'];
                        }
                    }

                    $keyBuffer=$chapterKey->selectData('ChapterID,Keyword','SubjectID='.$SubjectID);
                    foreach($idList2 as $j=>$iIdList2){
                        $cpList=array();
                        if($klList[$iIdList2]){
                            $buffer=$chapterKl->where('KID in ('.implode(',',$klList[$iIdList2]).')')->select();
                            if($buffer){
                                foreach($buffer as $buffern){
                                    $cpList[]=$buffern['CID'];
                                }
                            }
                        }
                        foreach($keyBuffer as $k=>$jKeyBuffer){
                            if(strstr($keyList[$iIdList2],$jKeyBuffer['Keyword'])){
                                $cpList[]=$jKeyBuffer['ChapterID'];
                            }
                        }
                        if($cpList){
                            $cpList=array_filter(array_unique($cpList));
                            $cpList=$chapter->filterChapterID($cpList);
                            //写入章节
                            $testChapterReal->where('TestID='.$iIdList2)->delete();
                            foreach($cpList as $k=>$jCpList){
                                $data=array();
                                $data['TestID']=$iIdList2;
                                $data['ChapterID']=$jCpList;
                                $testChapterReal->data($data)->add();
                            }
                        }
                    }
                }
            }
        }
    }*/
    /* //为年级进行匹配
    public function setGradeDocTest(){
        $doc=M('Doc');
        $testattr=M('TestAttr');
        $testattrreal=M('TestAttrReal');
        $count=$doc->count('DocID');
        $start=7999;
        $count=15000;
        $perpage=50;
        $gradeArr=SS('grade');
        for($i=$start;$i<$count+1;){
            echo 'aa<br/>';
            $buffer=$doc->field('DocID,DocName')->where('1=1')->order('DocID asc')->limit($i.','.($perpage+1))->select();
            if($buffer){
                foreach($buffer as $buffern){
                    $gradeid=0;
                    foreach($gradeArr as $ii=>$gradeArrn){
                        if(strstr($buffern['DocName'],$gradeArrn['GradeName'])){
                            $gradeid=$ii;
                        }
                    }
                    if($gradeid==0) $gradeid=4;
                    //更新gradeid
                    $doc->data(array('DocID'=>$buffern['DocID'],'GradeID'=>$gradeid))->save();
                    $testattr->data(array('GradeID'=>$gradeid))->where('DocID='.$buffern['DocID'])->save();
                    $testattrreal->data(array('GradeID'=>$gradeid))->where('DocID='.$buffern['DocID'])->save();
                }
            }
            $i=$i+$perpage;
        }

    }*/
    /* //为没有学号的数据加上学号
    public function setStuNum(){
        $user=M('User');
        $Autoinc=$this->getModel('Autoinc');
        $buffer=$user->where('OrderNum="0"')->select();
        if($buffer){
            foreach($buffer as  $buffern){
                $OrderNum=$Autoinc->getOrderNum();
                if(!$OrderNum){
                    $this->showerror('生成学号失败！');
                    exit;
                }
                $data=array();
                $data['UserID'] = $buffern['UserID'];
                $data['OrderNum'] = $OrderNum;
                $user->data($data)->save();
            }
        }
    }*/
    /*更新试题属性
    public function setTestAttr(){
        set_time_limit(0);
        $TestAttr=M('TestAttr');
        $TestAttr11=M('TestAttr11');
        $TestAttrReal=M('TestAttrReal');
        $TestAttrReal11=M('TestAttrReal11');
        $count=$TestAttrReal11->count('TestID');
        $count=200000;
        $per=2000;
        for($i=147999;$i<$count;$i=$i+$per){
            $buffer=$TestAttrReal11->field('TestID,TestNum,TestStyle,OptionWidth,OptionNum')->limit($i.','.($per+2))->select();
            if($buffer){
                foreach($buffer as $buffern){
                    $data=array();
                    $data['TestID']=$buffern['TestID'];
                    $data['TestNum']=$buffern['TestNum'];
                    $data['TestStyle']=$buffern['TestStyle'];
                    $data['OptionWidth']=$buffern['OptionWidth'];
                    $data['OptionNum']=$buffern['OptionNum'];
                    $TestAttrReal->data($data)->save();
                    $TestAttr->data($data)->save();
                }
            }
        }
        $count=$TestAttr11->count('TestID');
        for($i=0;$i<$count;$i=$i+$per){
            $buffer=$TestAttr11->field('TestID,TestNum,TestStyle,OptionWidth,OptionNum')->limit($i.','.($per+2))->select();
            if($buffer){
                foreach($buffer as $buffern){
                    $data=array();
                    $data['TestID']=$buffern['TestID'];
                    $data['TestNum']=$buffern['TestNum'];
                    $data['TestStyle']=$buffern['TestStyle'];
                    $data['OptionWidth']=$buffern['OptionWidth'];
                    $data['OptionNum']=$buffern['OptionNum'];
                    $TestAttr->data($data)->save();
                    $TestAttrReal->data($data)->save();
                }
            }
        }
    }*/
    /*修改文档numbid的可排序性*/
    /*public function changeNumbID(){
        $test=M('Test');
        $buffer=$test->field('TestID,NumbID')->where('1=1')->select();
        foreach($buffer as $buffern){
            $tmparr=explode('-',$buffern['NumbID']);
            if(strlen($tmparr[1])==1){
                $tt=$tmparr[0].'-0'.$tmparr[1];
                $data=array();
                $data['TestID']=$buffern['TestID'];
                $data['NumbID']=$tt;
                $test->data($data)->save();
            }
        }
        $test=M('TestReal');
        $buffer=$test->field('TestID,NumbID')->where('1=1')->select();
        foreach($buffer as $buffern){
            $tmparr=explode('-',$buffern['NumbID']);
            if(strlen($tmparr[1])==1){
                $tt=$tmparr[0].'-0'.$tmparr[1];
                $data=array();
                $data['TestID']=$buffern['TestID'];
                $data['NumbID']=$tt;
                $test->data($data)->save();
            }
        }
    }*/

    /*获取试卷属性 for 模板组卷*/
     /*public function getDocAttr(){
        $TypeID=5;//仿真高考
        $IfDefault=1; //是否是默认模板0是

        $user=array();
        $user[12]='yuwen@qq.com';
        $user[13]='shuxue@qq.com';
        $user[14]='yingyu@qq.com';
        $user[15]='wuli@qq.com';
        $user[16]='huaxue@qq.com';
        $user[17]='shengwu@qq.com';
        $user[18]='zhengzhi@qq.com';
        $user[19]='lishi@qq.com';
        $user[20]='27148@qq.com';

        //缓存
        $typesArray=SS('types');
        $typesSubjectArray=SS('typesSubject');

        $diffArray=C('WLN_TEST_DIFF');

        $doc=M('Doc');
        $base=$this->getModel('Base');
        $testreal=M('TestReal');
        $dirtemplate=M('DirTemplate');
        $testklreal=M('TestKlReal');
        $buffer=$doc->where('DocYear=2015 and TypeID=1 and IfIntro=1')->select();

        $tmpStr=''; //记录临时数据
        $juan=0; //分卷序号
        foreach($buffer as $buffern){
            $output=array(); //输出数组
            $data=array(); //输出数组
            $SubjectID=$buffern['SubjectID'];
            $UserName=$user[$buffern['SubjectID']];

            $TempName=$buffern['DocName'];

            $data['TempName']=$TempName;
            $data['UserName']=$UserName;
            $data['SubjectID']=$SubjectID;
            $data['TypeID']=$TypeID;
            $data['IfDefault']=$IfDefault;
            $data['OrderID']=99;
            $data['AddTime']=time();

            $output['chooseattr']=1;
            $output['doctype']='2,3';
            $output['tempname']=$buffern['DocName'];
            $output['maintitle']=$buffern['DocName'];
            $output['subtitle']='高考仿真卷';
            $output['notice']="1．答题前填写好自己的姓名、班级、考号等信息<br/>2．请将答案正确填写在答题卡上";//注意事项

            $tmparr=$testreal->field('t.TestID,t.NumbID,a.Diff,a.TypesID,a.diff,a.TestNum,a.TestStyle')->table('zj_test_real t')->join('zj_test_attr_real a on a.TestID=t.TestID')->where('t.DocID='.$buffern['DocID'])->order('t.NumbID asc')->select();


            $testlist=array();
            foreach($tmparr as $i=>$tmparrn){
                $testlist[]=$tmparrn['TestID'];
            }
            $kl_buffer=$testklreal->where('TestID in ('.implode(',',$testlist).')')->select();
            $testkllist=array();
            foreach($kl_buffer as $kl_buffern){
                $testkllist[$kl_buffern['TestID']][]=$kl_buffern['KlID'];
            }
            $arr=array();
            foreach($tmparr as $i=>$tmparrn){
                $juan=$typesArray[$tmparrn['TypesID']]['Volume']-1;
                if($tmpStr===''){
                    $tmpStr=$tmparrn['TypesID'];
                    $arr[$juan][$tmpStr]['questypename']=$typesArray[$tmpStr]['TypesName'];//题型名称
                    $arr[$juan][$tmpStr]['typeid']=$tmpStr;//题型id
                    $arr[$juan][$tmpStr]['questypedes']="(题型注释)";//题型注释
                }else if($tmpStr!=$buffern['TypesID']){
                    $tmpStr=$tmparrn['TypesID'];
                    $arr[$juan][$tmpStr]['questypename']=$typesArray[$tmpStr]['TypesName'];//题型名称
                    $arr[$juan][$tmpStr]['typeid']=$tmpStr;//题型id
                    $arr[$juan][$tmpStr]['questypedes']="(题型注释)";//题型注释
                }
                $dscore=$typesArray[$tmpStr]['DScore'];
                if($tmparrn['TestNum']==0)
                    $nums=1;//小题数
                else
                    $nums=$tmparrn['TestNum'];//小题数

                $arr[$juan][$tmpStr][$i]['nums']=$nums;
                $arr[$juan][$tmpStr][$i]['rounds']=implode(',',$testkllist[$tmparrn['TestID']]); //隐藏的考察范围 多个以竖线间隔
                $scoren=array();
                for($ii=0;$ii<$nums;$ii++){
                    $scoren[]=$dscore;
                }
                $arr[$juan][$tmpStr][$i]['scores']=implode(',',$scoren); //分值 多个以竖线间隔
                $arr[$juan][$tmpStr][$i]['diff'] =R('Common/TestLayer/diff2Int',array($tmparrn['Diff'],$diffArray));//难度
                $arr[$juan][$tmpStr][$i]['testchoose']=$tmparrn['TestStyle']; //选择类型 0为不考虑此项
                $arr[$juan][$tmpStr][$i]['ifchoose'] ="0";//是否选做 0为不是 否则就是几选【几】中的【几】
                $arr[$juan][$tmpStr][$i]['choosenum'] ="0";//第几个选做题 选做题出现的顺序 相同非零值代表同一组选做题
            }
            $juan1=0;$juan2=0;
            $now=0;
            $arrtmp=array();
            foreach($typesSubjectArray[$SubjectID] as $ii=>$a){
                if($a['Volume']==1){
                    $now=$juan1;
                    $juan1++;
                }else if($a['Volume']==2){
                    $now=$juan2;
                    $juan2++;
                }
                if($arr[$a['Volume']-1][$a['TypesID']]){
                    //需要重置
                    $tmparr2=array();
                    if($arr[$a['Volume']-1][$a['TypesID']]){
                        $jjj=0;
                        foreach($arr[$a['Volume']-1][$a['TypesID']] as $iii=>$aaa){
                            if($iii==='questypename'){
                                $arrtmp[$a['Volume']-1][$now]['questypename']=$aaa;
                            }else if($iii==='typeid')  $arrtmp[$a['Volume']-1][$now]['typeid']=$aaa;
                            else if($iii==='questypedes')  $arrtmp[$a['Volume']-1][$now]['questypedes']=$aaa;
                            else{
                                $tmparr2[$a['Volume']-1][$now][$jjj]=$aaa;
                                $jjj++;
                            }
                        }
                        $arrtmp[$a['Volume']-1][$now]=array_merge($arrtmp[$a['Volume']-1][$now],$tmparr2[$a['Volume']-1][$now]);

                    }
                }
            }
            $output[0]=$arrtmp[0];
            $output[0]['parthead']="第I卷（选择题）";//分卷名
            $output[0]['partheaddes']="请点击修改第I卷的文字说明";//分卷描述
            $output[1]=$arrtmp[1];
            $output[1]['parthead']="第II卷（非选择题）";//分卷名
            $output[1]['partheaddes']="请点击修改第II卷的文字说明";//分卷描述

            //$data['UserName']='admin';
            $data['Content']=serialize($output);
            $tt=$dirtemplate->where('TempName="'.$TempName.'" and UserName="'.$UserName.'"')->select();
            //exit(print_r($data));
            if(!$tt){
                $dirtemplate->data($data)->add();
            }else{
                $data['TempID']=$tt[0]['TempID'];
                $dirtemplate->data($data)->save();
            }
        }
    }*/
    /*替换学校*/
    /*public function changeschool(){
        $UpdateAreaSchool=$this->getModel('UpdateAreaSchool');
        $UpdateAreaSchool->updateUserOldArea();
        exit();
        exit($UpdateAreaSchool->getSchoolName(2000));

    }*/
    /*处理已经被替换过的试题是否再次被提取 而留下不必要的数据在replace*/
    /*public function checkreplacetest(){
        $Testreplace=$this->getModel('Testreplace');
        $Test=$this->getModel('Test');
        $TestReal=$this->getModel('TestReal');
        $Doc=$this->getModel('Doc');
        $buffer=$Testreplace->field('TestID,DocPath')->select();
        foreach($buffer as $i=>$iBuffer){
            $bufferx=$Test->where('TestID='.$iBuffer['TestID'])->select();
            if($bufferx){
                if(strstr($bufferx[0]['Test'],'<img')){
                    if(!strstr($bufferx[0]['Test'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                        echo $iBuffer['TestID'].'--delete<br/>';
                        $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                    }
                }else if(strstr($bufferx[0]['Answer'],'<img')){
                    if(!strstr($bufferx[0]['Answer'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                        echo $iBuffer['TestID'].'--delete<br/>';
                        $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                    }
                }else if(strstr($bufferx[0]['Analytic'],'<img')){
                    if(!strstr($bufferx[0]['Analytic'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                        echo $iBuffer['TestID'].'--delete<br/>';
                        $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                    }
                }else if(strstr($bufferx[0]['Remark'],'<img')){
                    if(!strstr($bufferx[0]['Remark'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                        $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                        $Doc->deleteAllFile($iBuffer);
                        echo $iBuffer['TestID'].'--delete<br/>';
                    }
                }else{

                }
            }else{
                $bufferx=$TestReal->where('TestID='.$iBuffer['TestID'])->select();
                if($bufferx){
                    if(strstr($bufferx[0]['Test'],'<img')){
                        if(!strstr($bufferx[0]['Test'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                            echo $iBuffer['TestID'].'--delete<br/>';
                            $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                        }
                    }else if(strstr($bufferx[0]['Answer'],'<img')){
                        if(!strstr($bufferx[0]['Answer'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                            echo $iBuffer['TestID'].'--delete<br/>';
                            $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                        }
                    }else if(strstr($bufferx[0]['Analytic'],'<img')){
                        if(!strstr($bufferx[0]['Analytic'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                            echo $iBuffer['TestID'].'--delete<br/>';
                            $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                        }
                    }else if(strstr($bufferx[0]['Remark'],'<img')){
                        if(!strstr($bufferx[0]['Remark'],preg_replace('/\.docx|\.doc/i','',$iBuffer['DocPath']))){
                            echo $iBuffer['TestID'].'--delete<br/>';
                            $Testreplace->where('TestID='.$iBuffer['TestID'])->delete();
                            $Doc->deleteAllFile($iBuffer);
                        }
                    }else{

                    }
                }else{
                    echo $iBuffer['TestID'].'--no<br/>';
                }
            }
        }
    }*/
    /*采集*/
    /*public function curlxk(){
        set_time_limit(0);
        $bankid=$_GET['bankid'];
        $page=$_GET['page'];
        if(empty($bankid)) $bankid=3;
        if(empty($page)) $page=1;
        $post_url='http://www.zujuan.com/Web/Handler1.ashx?action=paperlistquery';
        $post_data=array('bankid'=>$bankid,'learngrade'=>0,'papertype'=>0,'province'=>-1,'curpage'=>$page,'pagesize'=>10);
        $output=$this->_myurl($post_url,$post_data);

        $tmp_arr=explode('###',$output);
        $pagecount=str_replace('{papercount:','',$tmp_arr[0]);
        $pagecount=str_replace('}','',$pagecount);
        $pagecount=$pagecount/10+1;
        $buffer1=json_decode($tmp_arr[1],true);
        if($buffer1){
            foreach($buffer1 as $buffer1n){
                $pageName=$buffer1n['Title'];
                $post_url='http://www.zujuan.com/Web/Handler1.ashx?action=getremotepaper';
                $post_data=array('bankid'=>$bankid,'paperid'=>$buffer1n['ID']);
                $output=$this->_myurl($post_url,$post_data);

                $tmp_arr=explode('src=\"',$output);
                $tmp_arr2=array();
                if(count($tmp_arr)>1){
                    foreach($tmp_arr as $ii=>$tmp_arrn){
                        if($ii==0) continue;
                        $tmp_arr1=explode('\"',$tmp_arrn);
                        $tmp_str=('http://static.zujuan.com/'.str_replace('\\/','/',$tmp_arr1[0]));
                        $tmp_arr3=explode('.files',$tmp_str);
                        $tmp_arr2[]=$tmp_arr3[0];
                    }
                    $tmp_arr2=array_filter(array_unique($tmp_arr2));
                }
                if($tmp_arr2){
                    $xx=1;
                    foreach($tmp_arr2 as $tmp_arr2n){
                        $tmp_str=$tmp_arr2n.'.docx';
                        $tmp_str1=$tmp_arr2n.'.doc';
                        $filecon=file_get_contents($tmp_str);
                        if(!$filecon) $filecon=file_get_contents($tmp_str1);
                        $filepath=realpath('./').'/'.$bankid.'/';
                        if(!file_exists($filepath)) mkdir($filepath,777);
                        echo $pageName.':'.strlen($filecon).':'.$tmp_str.'<br/>';
                        if($xx==1) $filepath.=iconv('UTF-8','GBK//IGNORE',$pageName).'.docx';
                        else  $filepath.=iconv('UTF-8','GBK//IGNORE',$pageName.$xx).'.docx';
                        if($filecon){
                            while(file_exists($filepath)){
                                $filepath=str_replace('.doc','_.doc',$filepath);
                            }
                            file_put_contents($filepath,$filecon);
                            $xx++;
                        }
                    }
                }
            }
        }

        if($page>$pagecount){
            $page=1;
            $bankid++;
        }else{
            $page++;
        }
        if($bankid>=100) exit();
        exit('<script>location.href="'.__MODULE__.'-Index-curlxk-page-'.$page.'-bankid-'.$bankid.'";</script>');
    }
    protected function _myurl($post_url,$post_data){
        $cookie_jar = tempnam('./tmp','cookie');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0");
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
        $result = curl_exec($curl);
        $error = curl_error($curl);
        $output = $error ? $error : $result;
        return $output;
    }*/

       /**
     * 替换数据库zj_test，zj_test_real，zj_testDoc中指定科目的试卷内容
     * @param string $oldWords 原来的词
     * @param string $newWords 要替换的新词
     * @param int $subjectID 科目编号
     * @author demo

    public function strReplace($oldWords,$newWords,$subjectID){
        set_time_limit(0);
        $testAttrReal = $this->getModel('TestAttrReal');
        $replaceID = array();
        $where = 'SubjectID='.$subjectID;
        $replaceID[1] = $testAttrReal->field('TestID')->where($where)->select();
        $testAttr = $this->getModel('TestAttr');
        $replaceID[2] = $testAttr->field('TestID')->where($where)->select();
        $testDoc = $this->getModel('TestDoc');
        $tmparr=array_merge($replaceID[1],$replaceID[2]);
        foreach($tmparr as $testID){
                   $where = "TestID=".$testID['TestID'];
                   $testDocStr = $testDoc->field('DocTest,DocAnalytic,DocAnswer,DocRemark')->where($where)->find();
                   if($testDocStr){
                       $newStr = array();
                       $newStr['DocTest'] = str_replace($oldWords,$newWords,$testDocStr['DocTest']);
                       $newStr['DocAnalytic'] = str_replace($oldWords,$newWords,$testDocStr['DocAnalytic']);
                       $newStr['DocAnswer'] = str_replace($oldWords,$newWords,$testDocStr['DocAnswer']);
                       $newStr['DocRemark'] = str_replace($oldWords,$newWords,$testDocStr['DocRemark']);
                       $testDoc->where($where)->save($newStr);
                   }
        }
        $test = $this->getModel('Test');
        foreach($replaceID[2] as $testID){
                   $where = "TestID=".$testID['TestID'];
                   $testDocStr = $test->field('Test,Analytic,Answer,Remark')->where($where)->find();
                   if($testDocStr){
                       $newStr = array();
                       $newStr['Test'] = str_replace($oldWords,$newWords,$testDocStr['Test']);
                       $newStr['Analytic'] = str_replace($oldWords,$newWords,$testDocStr['Analytic']);
                       $newStr['Answer'] = str_replace($oldWords,$newWords,$testDocStr['Answer']);
                       $newStr['Remark'] = str_replace($oldWords,$newWords,$testDocStr['Remark']);
                       $test->where($where)->save($newStr);
                   }
        }
        $testReal = $this->getModel('TestReal');
        foreach($replaceID[1] as $testID){
                   $where = "TestID=".$testID['TestID'];
                   $testDocStr = $testReal->field('Test,Analytic,Answer,Remark')->where($where)->find();
                   if($testDocStr){
                       $newStr = array();
                       $newStr['Test'] = str_replace($oldWords,$newWords,$testDocStr['Test']);
                       $newStr['Analytic'] = str_replace($oldWords,$newWords,$testDocStr['Analytic']);
                       $newStr['Answer'] = str_replace($oldWords,$newWords,$testDocStr['Answer']);
                       $newStr['Remark'] = str_replace($oldWords,$newWords,$testDocStr['Remark']);
                       $testReal->where($where)->save($newStr);
                   }
        }
    }*/

     /**
     * 更新zj_attr,zj_attr_real数据库中的testNum，OptionNum,testStyle，optionWidth字段数据
     * @ author
     */
    /*public function splitPart(){
        set_time_limit(0);
        $testInfo = $this->getdbinfo('Test');
        $testAttr = $this->getModel('TestAttr');
        foreach($testInfo as $k=>$test){
            $data = 'TestID='.$testInfo[$k]['TestID'];
            $updateData['TestNum'] = $testInfo[$k]['TestNum'];
            $updateData['OptionNum'] = $testInfo[$k]['OptionNum'];
            $updateData['TestStyle'] = $testInfo[$k]['TestStyle'];
            $updateData['OptionWidth'] = $testInfo[$k]['optionWidth'];
            $testAttr->where($data)->save($updateData);
        }
        $testRealInfo = $this->getdbinfo('TestReal');
        $testAttrReal = $this->getModel('TestAttrReal');
        foreach($testRealInfo as $k=>$test){
            $data = 'TestID='.$testRealInfo[$k]['TestID'];
            $updateData['TestNum'] = $testRealInfo[$k]['TestNum'];
            $updateData['OptionNum'] = $testRealInfo[$k]['OptionNum'];
            $updateData['TestStyle'] = $testRealInfo[$k]['TestStyle'];
            $updateData['OptionWidth'] = $testRealInfo[$k]['optionWidth'];
            $testAttrReal->where($data)->save($updateData);
        }
    }*/
/*protected function getdbinfo($dataBase){
    $test = M($dataBase);
    $judge = $this->getModel('TestJudge');
    $start=$_GET['start']; //40000
    $perpage=$_GET['perpage']; //5002
    if(!$start && !$perpage) exit('请输出参数');
    $testInfo = $test->field('Test,TestID')->where('TestID>187774')->order('TestID asc')->limit($start,$perpage)->select();
    foreach($testInfo as $k=>$testDetail){
        $result = $judge->getJudgeByTestID($testDetail['TestID']);
        $testInfo[$k]['TestNum'] = count($result);
        $testInfo[$k]['TestID']=$testDetail['TestID'];
        //该题不在test_judge表
        if($testInfo[$k]['TestNum'] == 0){
            $strInfo = $judge->formatStrToArr($testDetail['Test']);
            if(count($strInfo) >= 3){
                $pregTime = preg_match_all('/([A][\.．].*)/U',$testDetail['Test']);
                if($pregTime>1){
                    $testInfo[$k]['optionWidth'] = 1;
                    $testInfo[$k]['OptionNum'] = count($strInfo)-1;
                    $testInfo[$k]['TestStyle'] = 3;
                }else{
                    $testInfo[$k]['optionWidth'] = $this->getMaxOption($testDetail['Test']);
                    $testInfo[$k]['OptionNum'] = count($strInfo)-1;
                    $testInfo[$k]['TestStyle'] = 1;
                }
            }else{
                $testInfo[$k]['optionWidth'] = 0;
                $testInfo[$k]['OptionNum'] = 0;
                $testInfo[$k]['TestStyle'] = 3;
            }
        }else{
            $selOption = $judge->xtnum($testDetail['Test'],3);
            array_shift($selOption);
            if($selOption == 0){
                $strInfo = $judge->formatStrToArr($testDetail['Test']);
                if(count($strInfo)>1){
                    $testInfo[$k]['TestStyle'] = 3;
                    $testInfo[$k]['OptionNum'] = count($strInfo)-1;
                    $testInfo[$k]['optionWidth'] = 1;
                }else{
                    $testInfo[$k]['TestStyle'] = 3;
                    $testInfo[$k]['OptionNum'] = 0;
                    $testInfo[$k]['optionWidth'] = 0;
                }
                unset($testInfo[$k]['Test']);
                continue;
            }
            $allOption = 0;
            $noneOption = 0;
            $optionNum = '';
            $optionWidth = '';
            foreach($selOption as $partInfo){
                $partTest = $judge->formatStrToArr($partInfo);
                if(count($partTest)>=3){
                    $width = $this->getMaxOption($partInfo).',';
                    $num = (count($partTest)-1).',';
                }else{
                    $width = '0,';
                    $num = '0,';
                    $allOption = 1;
                    $noneOption++;
                }
                $optionNum .= $num;
                $optionWidth .= $width;
            }
            if($allOption==0){
                $testInfo[$k]['TestStyle'] = 1;
                $testInfo[$k]['OptionNum'] = trim($optionNum,',');
                $testInfo[$k]['optionWidth'] = trim($optionWidth,',');
            }elseif($noneOption==count($selOption)){
                $testInfo[$k]['TestStyle'] = 3;
                $testInfo[$k]['OptionNum'] = 0;
                $testInfo[$k]['optionWidth'] = 0;
            }elseif($allOption==1 && $noneOption!=count($selOption)){
                $testInfo[$k]['TestStyle'] = 2;
                $testInfo[$k]['OptionNum'] = trim($optionNum,',');
                $testInfo[$k]['optionWidth'] = trim($optionWidth,',');
            }
        }
    }
    unset($testInfo[$k]['Test']);
    return $testInfo;
}
protected function getMaxOption($testStr){
    $judge = $this->getModel('TestJudge');
    $testStrPart = $judge->formatOptions($testStr,530,1,2);
    $testInfo = max($testStrPart[1]);
    return $testInfo;
}*/
/*
//重新载入试题html数据 更新上下标
public function reloadreplacehtml(){
    $num=$_GET['num'];
    if(!is_numeric($num)) exit('success');
    $Testreplace = $this->getModel('Testreplace');
    $buffer=$Testreplace->order('TestID asc')->limit($num,1)->select();
    if(!$buffer) exit('success');
    $DocHtmlPath=$buffer[0]['DocHtmlPath'];
    $DocFilePath=$buffer[0]['DocFilePath'];
    $TestID= $buffer[0]['TestID'];
    //替换试题内容
            $Doc=$this->getModel('Doc');
            $data = array ();
            $data['TestID'] =$TestID;

            $Testtag = $this->getModel('Testtag');
            $buffer = $Testtag->order('OrderID asc')->select();

            $start = array ();
            $testfield = array ();
            foreach ($buffer as $buffern) {
                $start[] = $buffern['DefaultStart'];
                $testfield[] = $buffern['TestField'];
            }
            $html = $Doc->getDocContent($DocHtmlPath);  //获取html数据

            $arr_html = $Doc->division($html, $start,2); //分割
            $newarr = $Doc->changeArrFormat($arr_html); //html过滤

            $testfield_arr = C('WLN_TEST_FIELD'); //数据表字段对应数组

            //单条数据记录
            foreach ($newarr[0] as $ii => $nn) {
                if(!strstr($testfield[$ii],'属性')){
                    $data[$testfield_arr[$testfield[$ii]]] = $nn;
                }
            }
            $Test=M('Test');
            $TestReal=M('TestReal');
            $buffer=$TestReal->where('TestID='.$TestID)->select();
            if($buffer)
            $TestReal->data($data)->save();
            else
            $Test->data($data)->save();

    exit($TestID.':success;next;<script>location.href="'.__MODULE__.'-Index-reloadreplacehtml-num-'.($num+1).'";</script>');
}
public function reloadhtml(){
    $num=$_GET['num'];
    if(!is_numeric($num)) exit('success');
    $Doc = $this->getModel('Doc');
    $buffer=$Doc->field('DocID')->order('DocID asc')->limit($num,1)->select();
    if(!$buffer) exit('success');
    $DocID=$buffer[0]['DocID'];

    $Testtag = $this->getModel('Testtag');
    $Doc = $this->getModel('Doc');
    $buffer = $Testtag->order('OrderID asc')->select();
    $start = array ();
    $testfield = array ();
    foreach ($buffer as $buffern) {
        $start[] = $buffern['DefaultStart'];
        $testfield[] = $buffern['TestField'];
    }

    $edit = $Doc->where('DocID=' . $DocID)->limit(1)->select();
    $html = $Doc->getDocContent($edit[0]['DocHtmlPath']); //获取html数据


    if(!strstr($html,'<sub>') and !strstr($html,'<sup>')){
        exit($DocID.':success;next;<script>location.href="'.__MODULE__.'-Index-reloadhtml-num-'.($num+1).'";</script>');
        exit();
    }
    $arr_html = $Doc->division($html, $start,2); //分割
    $newarr = $Doc->changeArrFormat($arr_html); //html过滤

        $Test = $this->getModel('Test');
        $TestReal = M('TestReal');
        $testfield_arr = C('WLN_TEST_FIELD'); //数据表字段对应数组

        foreach($newarr as $key=>$newarrn){
            $idList[]=$key+1;
        }
        foreach ($idList as $idn) {
            $data = array ();
            //是否入库过，如果入库过则覆盖
            $NumbID=$DocID.'-'.$idn;
            $testnumb = array ();
            $testnumb = $TestReal->where('NumbID="' . $NumbID . '"')->select();
            $nowtable='';
            if ($testnumb) {
                    $data['TestID'] = $testnumb[0]['TestID'];
                    $nowtable=$TestReal;
            }else{
                //是否提取过，如果提取过则覆盖
                $testnumb = $Test->where('NumbID="' . $NumbID . '"')->select();
                if ($testnumb) {
                    $data['TestID'] = $testnumb[0]['TestID'];
                    $nowtable=$Test;
                }else{
                    continue;
                }
            }
            //单条数据记录
            $tmp_arr=array();
            foreach ($newarr[$idn -1] as $ii => $nn) {
                if(!strstr($testfield[$ii],'属性')){
                    $data[$testfield_arr[$testfield[$ii]]] = $nn;
                }
            }
            if (!empty ($data['TestID'])) {
                $TestID = $data['TestID'];
                $nowtable->data($data)->save();
            }
        }
        exit($DocID.':success;'.$edit[0]["DocFilePath"].'next;<script>location.href="'.__MODULE__.'-Index-reloadhtml-num-'.($num+1).'";</script>');
}
public function reloaddoc(){
    set_time_limit(0);
    $dir=realpath('./').'/Uploads/0524';
    $doc=$this->getModel('Doc');
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
            if($file!='.' and $file!='..' and strstr($file,'.doc')){
                $word = new COM("Word.Application") or die("无法打开 MS Word");
                $word->visible = 0;
                echo ($dir.'/'.$file).'<br/>';
                $wfilepath=$dir.'/'.$file;
                $word->Documents->Open($wfilepath) or die("无法打开这个文件");

                $htmlpath = substr($wfilepath, 0, strrpos($wfilepath, '.'));
                $word->ActiveDocument->SaveAs($htmlpath.'.htm', 8);
                $word->quit(0);
            }
            }
            closedir($dh);
        }
    }
}
//修复数据库测试类型重复
public function judgeRepeat() {
    set_time_limit(0);
    $testJudge=M('TestJudge');
    $buffer=$testJudge->order('TestID ASC,JudgeID ASC,OrderID ASC')->select();
    $tmpTest=0;
    $tmpOrder=0;
    $tmpAll=array();
    foreach($buffer as $buffern){
        $tmpAll[$buffern['TestID']][]=$buffern;
        if($tmpTest!=$buffern['TestID']){
            $tmpTest=$buffern['TestID'];
            $tmpOrder=$buffern['OrderID'];
            $tmpOrder++;
        }else{
            if($tmpOrder!=$buffern['OrderID']){
                $testJudge->where('JudgeID='.$buffern['JudgeID'])->data(array('OrderID'=>$tmpOrder))->save();
            }
            $tmpOrder++;
        }
    }

    $TestAttr=M('TestAttr');
    $Test=$this->getModel('Test');
    $buffer=$Test->field('t.TestID,t.Test,t.Answer,a.IfChoose')->table('zj_test t')->join('zj_test_attr a on a.TestID=t.TestID')->order('t.TestID asc')->select();
    foreach($buffer as $buffern){
            $tmp_test=$Test->judgeChoose(array('Test'=>$buffern['Test'],'Answer'=>$buffern['Answer']));
            $TestID=$buffern['TestID'];
            if(is_array($tmp_test)){
                    //记录试题的复合题测试类型
                    $IfChoose=$tmp_test[0];
                    $dataj=array();
                    if(empty($tmpAll[$TestID])){
                        $dataj=array();
                        foreach($tmp_test[1] as $ii=>$nn){
                            $dataj['TestID']=$TestID;
                            $dataj['OrderID']=$ii+1;
                            $dataj['IfChoose']=$nn;
                            $testJudge->data($dataj)->add();
                        }
                    }else if(count($tmpAll[$TestID])>count($tmp_test[1])){
                        //删除多余数据
                        $tmpListID=array();
                        for($i=count($tmp_test[1]);$i<count($tmpAll[$TestID]);$i++){
                           $tmpListID[]=$tmpAll[$TestID][$i]['JudgeID'];
                        }
                        $testJudge->where('JudgeID in ('.implode(',',$tmpListID).')')->delete();
                    }else if(count($tmpAll[$TestID])<count($tmp_test[1])){
                        $dataj=array();
                        foreach($tmp_test[1] as $ii=>$nn){
                            if($ii>=count($tmpAll[$TestID])){
                                $dataj['TestID']=$TestID;
                                $dataj['OrderID']=$ii+1;
                                $dataj['IfChoose']=$nn;
                                $testJudge->data($dataj)->add();
                            }
                        }
                    }
                    echo $TestID.':'.implode(',',$tmpListID).'<br/>';
                    $data=array();
                    $data['IfChoose'] = 1;
                    $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }else{
                    $data=array();
                    $data['IfChoose'] = $tmp_test;
                    $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }
    }

    $TestAttr=M('TestAttrReal');
    $Test=$this->getModel('TestReal');
    $buffer=$Test->field('t.TestID,t.Test,t.Answer,a.IfChoose')->table('zj_test_real t')->join('zj_test_attr_real a on a.TestID=t.TestID')->order('t.TestID asc')->select();
    foreach($buffer as $buffern){
            $tmp_test=$Test->judgeChoose(array('Test'=>$buffern['Test'],'Answer'=>$buffern['Answer']));
            $TestID=$buffern['TestID'];
            if(is_array($tmp_test)){
                    //记录试题的复合题测试类型
                    $IfChoose=$tmp_test[0];
                    $dataj=array();
                    if(empty($tmpAll[$TestID])){
                        $dataj=array();
                        foreach($tmp_test[1] as $ii=>$nn){
                            $dataj['TestID']=$TestID;
                            $dataj['OrderID']=$ii+1;
                            $dataj['IfChoose']=$nn;
                            $testJudge->data($dataj)->add();
                        }
                    }else if(count($tmpAll[$TestID])>count($tmp_test[1])){
                        //删除多余数据
                        $tmpListID=array();
                        for($i=count($tmp_test[1]);$i<count($tmpAll[$TestID]);$i++){
                           $tmpListID[]=$tmpAll[$TestID][$i]['JudgeID'];
                        }
                        $testJudge->where('JudgeID in ('.implode(',',$tmpListID).')')->delete();
                    }else if(count($tmpAll[$TestID])<count($tmp_test[1])){
                        $dataj=array();
                        foreach($tmp_test[1] as $ii=>$nn){
                            if($ii>=count($tmpAll[$TestID])){
                                $dataj['TestID']=$TestID;
                                $dataj['OrderID']=$ii+1;
                                $dataj['IfChoose']=$nn;
                                $testJudge->data($dataj)->add();
                            }
                        }
                    }
                    echo $TestID.':'.implode(',',$tmpListID).'<br/>';
                    $data=array();
                    $data['IfChoose'] = 1;
                    $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }else{
                    $data=array();
                    $data['IfChoose'] = $tmp_test;
                    $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }
    }
}

//修改数据库测试类型
public function judge() {
    set_time_limit(0);
    $test_judge=M('TestJudge');
    $TestAttr=M('TestAttr');
    $Test=$this->getModel('Test');
    $buffer=$Test->order('TestID asc')->select();
    foreach($buffer as $buffern){
        if($buffern['IfChoose']==0){
            $tmp_test=$Test->judgeChoose(array('Test'=>$buffern['Test'],'Answer'=>$buffern['Answer']));
            $TestID=$buffern['TestID'];
            if(is_array($tmp_test)){
                    //记录试题的复合题测试类型
                    $IfChoose=$tmp_test[0];
                    $dataj=array();
                    foreach($tmp_test[1] as $ii=>$nn){
                        $dataj['TestID']=$TestID;
                        $dataj['OrderID']=$ii+1;
                        $dataj['IfChoose']=$nn;
                        $test_judge->data($dataj)->add();
                    }
            }
            else $IfChoose=$tmp_test;
            if($IfChoose!=0){
                $data=array();
                $data['IfChoose'] = $IfChoose;
                $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }
        }
    }
    $TestAttr=M('TestAttrReal');
    $Test=$this->getModel('TestReal');
    $buffer=$Test->order('TestID asc')->select();
    foreach($buffer as $buffern){
        if($buffern['IfChoose']==0){
            $tmp_test=$Test->judgeChoose(array('Test'=>$buffern['Test'],'Answer'=>$buffern['Answer']));
            $TestID=$buffern['TestID'];
            if(is_array($tmp_test)){
                    //记录试题的复合题测试类型
                    $IfChoose=$tmp_test[0];
                    $dataj=array();
                    foreach($tmp_test[1] as $ii=>$nn){
                        $dataj['TestID']=$TestID;
                        $dataj['OrderID']=$ii+1;
                        $dataj['IfChoose']=$nn;
                        $test_judge->data($dataj)->add();
                    }
            }
            else $IfChoose=$tmp_test;
            if($IfChoose!=0){
                $data=array();
                $data['IfChoose'] = $IfChoose;
                $TestAttr->data($data)->where('TestID='.$TestID)->save();
            }
        }
    }
}
//取消复合题中全部都是非选择题的题型
public function checkfht(){
    $test_judge = M('TestJudge');
    $testattr = M('TestAttr');
    $testattrreal = M('TestAttrReal');
    $buffer=$test_judge->order('TestID asc')->select();

    $tmp_arr=array();
    foreach($buffer as $buffern){
        $tmp_arr[$buffern['TestID']][]=$buffern['IfChoose'];
    }
    foreach($tmp_arr as $key=>$tmp_arrn){
        $flag=0;
        foreach($tmp_arrn as $tmp_arrnn){
            if($tmp_arrnn!=0) $flag=1;
        }
        if($flag==0){
            $test_judge->where('TestID ='. $key)->delete();
            $testattr->data(array('IfChoose'=>0))->where('TestID ='. $key)->save();
            $testattrreal->data(array('IfChoose'=>0))->where('TestID ='. $key)->save();
        }
    }
}

public function testupload(){
    echo '<form action="?" enctype="multipart/form-data" method="post" ><input name="filedata" type="file" value=""/><input name="submit" type="submit" value="submit" /></form>';

    if(IS_POST){
        //上传文件到本地
        $Doc=$this->getModel('Doc');
        if (!empty ($_FILES['filedata']['name']) && !empty ($_FILES['filedata']['size'])) {
            //上传word
            $DocPath = $Doc->upload();
            $realpath = realpath('./') . $DocPath;
            if (!file_exists($realpath)) {
                $this->showerror($DocPath);
            }
        }
        //本地文件存到远程
        exit($Doc->upFileToServer($realpath,'word',''));
    }
}

public function insertChapter(){
    set_time_limit(0);
    $path=realpath('./').'/chapter/list.txt';
    $str=iconv('GBK','UTF-8',file_get_contents($path));
    $arr=explode("\n",$str);
    $arr=array_filter($arr);

    foreach($arr as $arrn){
        $str=explode('|',$arrn);
        $SubjectName=$str[0];
        $str[2]=preg_replace("/\r/",'',$str[2]);
        $SubjectID=19;
        $banben=$str[1];
        //添加版本
        $ChapterID=$this->insertData($SubjectID,$banben,0,0);

        if(!$ChapterID){
            echo '版本: '.$banben.' 存在或有误<br>';
            continue;
        }

        $file=dirname($path).'/'.iconv('UTF-8','GBK//IGNORE',$str[2]);
        $tmp_str=iconv('GBK','UTF-8//IGNORE',file_get_contents($file));
        $tmp_arr=explode('tree-title',$tmp_str);

        //提取数据树形结构
        if($tmp_arr){
            $tree=array();
            $i=0;
            foreach($tmp_arr as $tmp_arrn){
                $tmp_id2=substr($tmp_arrn,0,1);
                $tmp_arrx=explode('title="',$tmp_arrn);
                $tmp_arrxx=explode('"',$tmp_arrx[1]);
                $title=$tmp_arrxx[0];
                if(empty($title)) continue;
                if(is_numeric($tmp_id2) && $tmp_id2>1){
                    if($tmp_id2==2){
                        $i++;
                        $j=0;
                        $tree[$i]['name']=$title;
                    }
                    if($tmp_id2==3){
                        $j++;
                        $k=0;
                        $tree[$i]['sub'][$j]['name']=$title;
                    }
                    if($tmp_id2==4){
                        $k++;
                        $tree[$i]['sub'][$j]['sub'][$k]['name']=$title;
                    }
                    if($tmp_id2==5){
                        $tree[$i]['sub'][$j]['sub'][$k]['sub'][]=$title;
                    }
                }
            }


            //属性结构入库
            if($tree){
                foreach($tree as $tree1){
                    $cid=$this->insertData($SubjectID,$tree1['name'],$ChapterID,0);
                    if(is_array($tree1['sub'])){
                        foreach($tree1['sub'] as $tree2){
                            $cid2=$this->insertData($SubjectID,$tree2['name'],$ChapterID,$cid);
                            if(is_array($tree2['sub'])){
                                foreach($tree2['sub'] as $tree3){
                                    $cid3=$this->insertData($SubjectID,$tree3['name'],$ChapterID,$cid2);
                                    if(is_array($tree3['sub'])){
                                        foreach($tree3['sub'] as $tree4){
                                            $this->insertData($SubjectID,$tree4,$ChapterID,$cid3);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    print_r($arr);
}
//插入Chapter数据
protected function insertData($SubjectID,$ChapterName,$TID,$PID){
    $chapter=$this->getModel('Chapter');
    $buffer=0;

    if(!$buffer){
        $data=array();
        $data['SubjectID']=$SubjectID;
        $data['ChapterName']=$ChapterName;
        if($TID==0){
            $buffer=$chapter->max('Rgt');
            if($buffer){
                $data['Lft']=$buffer+1;
                $data['Rgt']=$buffer+2;
            }else{
                $data['Lft']=0;
                $data['Rgt']=1;
            }
        }else{
            if($PID==0) $CID=$TID;
            else $CID=$PID;
            $buffer=$chapter->where('ChapterID='.$CID)->select();
            $chapter->where('Lft>'.$buffer[0]['Rgt'])->setInc('Lft',2);
            $chapter->where('Rgt>='.$buffer[0]['Rgt'])->setInc('Rgt',2);
            $data['Lft']=$buffer[0]['Rgt'];
            $data['Rgt']=$buffer[0]['Rgt']+1;
        }
        if(($ChapterID=$chapter->data($data)->add())===false){
            echo ($ChapterName.'添加失败！<br/>');
        }
        return $ChapterID;
    }
}
//批量加入知识点
public function insertKnowledge(){
    set_time_limit(0);
    $path=realpath('./').'/knowledge/list.txt';
    $str=iconv('GBK','UTF-8',file_get_contents($path));
    $arr=explode("\n",$str);
    $arr=array_filter($arr);

    foreach($arr as $arrn){
        $str=explode('|',$arrn);
        $SubjectName=$str[0];
        $str[1]=preg_replace("/\r/",'',$str[1]);
        //以下缓存暂时被去除了
        $subject_array=SS('subjectParentId');
        $SubjectID=$subject_array['初中'.$SubjectName];

        $file=dirname($path).'/'.iconv('UTF-8','GBK//IGNORE',$str[1]);
        $tmp_str=iconv('GBK','UTF-8//IGNORE',file_get_contents($file));
        $tmp_arr=explode('tree-title',$tmp_str);

        //提取数据树形结构
        if($tmp_arr){
            $tree=array();
            $i=0;
            foreach($tmp_arr as $tmp_arrn){
                $tmp_id2=substr($tmp_arrn,0,1);
                $tmp_arrx=explode('title="',$tmp_arrn);
                $tmp_arrxx=explode('"',$tmp_arrx[1]);
                $title=$tmp_arrxx[0];
                if(empty($title)) continue;
                if(is_numeric($tmp_id2) && $tmp_id2>1){
                    if($tmp_id2==2){
                        $i++;
                        $j=0;
                        $tree[$i]['name']=$title;
                    }
                    if($tmp_id2==3){
                        $j++;
                        $tree[$i]['sub'][$j]['name']=$title;
                    }
                    if($tmp_id2==4){
                        $tree[$i]['sub'][$j]['sub'][]=$title;
                    }
                }
            }
            //属性结构入库
            if($tree){
                foreach($tree as $tree1){
                    $cid=$this->insertKlData($SubjectID,$tree1['name'],0);
                    if(is_array($tree1['sub'])){
                        foreach($tree1['sub'] as $tree2){
                            $cid2=$this->insertKlData($SubjectID,$tree2['name'],$cid);
                            if(is_array($tree2['sub'])){
                                foreach($tree2['sub'] as $tree3){
                                    $this->insertKlData($SubjectID,$tree3,$cid2);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    print_r($arr);
}
//插入Knowledge数据
protected function insertKlData($SubjectID,$KlName,$PID){
    $Knowledge=$this->getModel('Knowledge');
        $data=array();
        $data['SubjectID']=$SubjectID;
        $data['KlName']=$KlName;
        $data['PID']=$PID;
        $data['IfTest']=0;
        if(($KlID=$Knowledge->data($data)->add())===false){
            echo ($KlName.'添加失败！<br/>');
        }
        return $KlID;
}*/
    /*
    //章节对应默认的知识点 加入testchapter表
    public function chapter2test(){
        ob_end_flush();
        echo 'asdasdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdassdasd<br/>';
        set_time_limit(0);
        //查找试题 test testreal
        $Test=M('Test');
        $TestKl=M('TestKl');
        $buffer=$Test->field('TestID')->order('TestID asc')->select();
        $kl_buffer=SS('knowledge');
        $Chapter=$this->getModel('Chapter');
        $ChapterKl=M('ChapterKl');
        $TestChapter=M('TestChapter');*/
        //试题对应知识点
        /*foreach($buffer as $kkk=>$buffern){
            $tmp_arr=$TestKl->where('TestID='.$buffern['TestID'])->select();
            if($tmp_arr){
                $tmp_arr2=array();
                foreach($tmp_arr as $tmp_arrn){
                    $tmp_arr2[]=$tmp_arrn['KlID'];
                }
                unset($tmp_arr);
                $tmp_arr3=$ChapterKl->where('KID in ('.implode(',',$tmp_arr2).') ')->select();
                unset($tmp_arr2);
                $tmp_id=array();
                //章节过滤
                if($tmp_arr3){
                    foreach($tmp_arr3 as $tmp_arr3n){
                        $tmp_id[]=$tmp_arr3n['CID'];
                    }
                    unset($tmp_arr3);
                    $tmp_id=array_unique(array_filter($tmp_id));

                    $tmp_id=$Chapter->filterChapterID($tmp_id);
                }
                //插入数据testchapter
                if($tmp_id){
                    foreach($tmp_id as $tmp_idn){
                        //检查是否重复
                        $tmp_arr4=$TestChapter->field('TestID')->where('TestID='.$buffern['TestID'].' and ChapterID='.$tmp_idn)->select();
                        if(!$tmp_arr4){
                            $data=array();
                            $data['TestID']=$buffern['TestID'];
                            $data['ChapterID']=$tmp_idn;
                            $TestChapter->data($data)->add();
                        }
                        unset($tmp_arr4);
                    }
                    unset($tmp_id);
                }
            }
            $memory1=memory_get_usage();
            echo ($memory1.'bbb<br/>');
            flush();
        }*/
        //查找试题 testreal
        /*$TestReal=M('TestReal');
        $TestKlReal=M('TestKlReal');
        $buffer=$TestReal->field('TestID')->order('TestID asc')->select();
        $TestChapterReal=M('TestChapterReal');
        //试题对应知识点
        foreach($buffer as $buffern){
            //检查是否已经有Testid的chapter
            $tmp_str=$TestChapterReal->where('TestID='.$buffern['TestID'])->select();
            if($tmp_str){
                continue;
            }
            echo  $buffern['TestID'].'<br/>';

            $tmp_arr=$TestKlReal->where('TestID='.$buffern['TestID'])->select();
            if($tmp_arr){
                $tmp_arr2=array();
                foreach($tmp_arr as $tmp_arrn){
                    $tmp_arr2[]=$tmp_arrn['KlID'];
                }
                $tmp_arr3=$ChapterKl->where('KID in ('.implode(',',$tmp_arr2).') ')->select();

                $tmp_id=array();
                //章节过滤
                if($tmp_arr3){
                    foreach($tmp_arr3 as $tmp_arr3n){
                        $tmp_id[]=$tmp_arr3n['CID'];
                    }
                    $tmp_id=array_unique(array_filter($tmp_id));
                    $tmp_id=$Chapter->filterChapterID($tmp_id);
                }

                //插入数据testchapter
                if($tmp_id){
                    foreach($tmp_id as $tmp_idn){
                            $data=array();
                            $data['TestID']=$buffern['TestID'];
                            $data['ChapterID']=$tmp_idn;
                            $TestChapterReal->data($data)->add();
                    }
                }
            }
        }
    }

    //试题入库
    public function testIntro(){
        $Test=$this->getModel('Test');
        $TestAttr=$this->getModel('TestAttr');
        $TestKl=$this->getModel('TestKl');
        $buffer=$Test->field('TestID')->where('1=1')->order('TestID asc')->select();
        $testlist=array();
        if($buffer){
            foreach($buffer as $buffern){
                $buffer2=$TestAttr->where('TestID='.$buffern['TestID'])->select();
                if($buffer2[0]['TypesID'] and $buffer2[0]['SubjectID'] and $buffer2[0]['Diff']!=0.000){
                    $buffer2=$TestKl->where('TestID='.$buffern['TestID'])->select();
                    if($buffer2){
                        //记录文档ID
                        $testlist[]=$buffern['TestID'];
                    }
                }
            }
        }
        if($testlist){
            //改变试题状态为审核
            $Test->where('TestID in ('.implode(',',$testlist).')')->data(array('Status'=>0))->save();
            //试题入库
            $TestReal=M('TestReal');
            $TestID=implode(',',$testlist);
            $TestReal->where('TestID in ('.$TestID.') ')->delete();

            $TestReal->execute('INSERT INTO zj_test_real SELECT *,unix_timestamp(now()) FROM zj_test WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('INSERT INTO zj_test_attr_real SELECT * FROM zj_test_attr WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('INSERT INTO zj_test_kl_real (KlID,TestID) SELECT KlID,TestID FROM zj_test_kl WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('INSERT INTO zj_test_chapter_real (ChapterID,TestID) SELECT ChapterID,TestID FROM zj_test_chapter WHERE TestID in ('.$TestID.') ');
            //删除原始数据
            $TestReal->execute('DELETE FROM zj_test WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('DELETE FROM zj_test_attr WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('DELETE FROM zj_test_kl WHERE TestID in ('.$TestID.') ');
            $TestReal->execute('DELETE FROM zj_test_chapter WHERE TestID in ('.$TestID.') ');
        }
    }
    //更新测试文档状态
    public function changeDocStatus(){
        $doc=$this->getModel('Doc');
        $buffer=$doc->where('IfTest=1')->select();
        if($buffer){
            $TestAttr=M('TestAttr');
            $TestAttrReal=M('TestAttrReal');
            foreach($buffer as $buffern){
                $tmp_arr1=$TestAttr->where('DocID='.$buffern['DocID'])->select();
                $tmp_arr2=$TestAttrReal->where('DocID='.$buffern['DocID'])->select();
                if(!$tmp_arr1 and $tmp_arr2 and count($tmp_arr2)>0){
                    $doc->where('DocID='.$buffern['DocID'])->data(array('IfTest'=>2))->save();
                }
            }
        }
    }
    //更新文档路径
    public function changeDocPath(){
        $doc=$this->getModel('Doc');
        $buffer=$doc->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['DocPath']=$this->getNewPath($buffern['DocPath']);
                $data['DocFilePath']=$this->getNewPath($buffern['DocFilePath']);
                $data['DocHtmlPath']=$this->getNewPath($buffern['DocHtmlPath']);
                $data['DocID']=$buffern['DocID'];
                $doc->data($data)->save();
            }
        }
        $Testreplace=$this->getModel('Testreplace');
        $buffer=$Testreplace->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['DocPath']=$this->getNewPath($buffern['DocPath']);
                $data['DocFilePath']=$this->getNewPath($buffern['DocFilePath']);
                $data['DocHtmlPath']=$this->getNewPath($buffern['DocHtmlPath']);
                $data['ReplaceID']=$buffern['ReplaceID'];
                $Testreplace->data($data)->save();
            }
        }
        $Docdown=$this->getModel('Docdown');
        $buffer=$Docdown->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['DocPath']=$this->getNewPath($buffern['DocPath']);
                $data['DownID']=$buffern['DownID'];
                $Docdown->data($data)->save();
            }
        }
    }
    //判断是否全部入库
    public function ifallintro(){
        $doc=M('Doc');
        $Test=M('Test');
        $TestReal=M('TestReal');
        $buffer=$doc->where('IfTask=1 and IfIntro=0')->select();
        if($buffer){
            $doc_list=array();
            foreach($buffer as $buffern){
                $a1=$Test->field('TestID')->where('DocID='.$buffern['DocID'])->select();
                $a2=$TestReal->field('TestID')->where('DocID='.$buffern['DocID'])->select();
                if(!$a1 && $a2){
                    //修改试卷的属性IfIntro为1
                    $doc_list[]=$buffern['DocID'];
                }
                if(count($doc_list)>=20){
                    $doc->where('DocID in ('.implode(',',$doc_list).')')->data(array('IfIntro'=>1,'IntroTime'=>time()))->save();
                    $doc_list=array();
                }
            }
        }
    }
    //更新任务数据
    public function updatetask(){
        $doc=M('Doc');
        $Test=M('Test');
        $TestReal=M('TestReal');
        $buffer=$doc->where('IfTask=0 and SubjectID=13')->select();
        if($buffer){
            $doc_list=array();
            $TeacherWork=M('TeacherWork');
            $TeacherWorkList=M('TeacherWorkList');
            foreach($buffer as $buffern){
                $a1=$Test->where('DocID='.$buffern['DocID'])->select();
                $a2=$TestReal->where('DocID='.$buffern['DocID'])->select();
                if(!$a1 && $a2){
                    //加入分配任务
                    $doc_list[]=$buffern['DocID'];
                }
                if(count($doc_list)>=20){
                    //修改doc的属性IfTask为1
                    $doc->where('DocID in ('.implode(',',$doc_list).')')->data(array('IfTask'=>1))->save();
                    //添加一条任务
                    $data=array();
                    $data['UserName']='5271348';
                    $data['AddTime']=time();
                    $data['Admin']='mine82';
                    $data['LastTime']=time();
                    $data['Status']=2;
                    $data['Content']="系统分配任务";
                    $data['SubjectID']=13;

                    $workid=$TeacherWork->data($data)->add();
                    //添加任务对于docid
                    $data=array();
                    $data['WorkID']=$workid;
                    $data['Status']=2;
                    foreach($doc_list as $doc_listn){
                        $data['DocID']=$doc_listn;
                        $TeacherWorkList->data($data)->add();
                    }
                    $doc_list=array();
                }
            }
        }
    }
    //更新数据路径
    public function changeTestPath(){
        set_time_limit(0);
        $test=$this->getModel('Test');
        $buffer=$test->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['Test']=$this->getNewTest($buffern['Test']);
                $data['Analytic']=$this->getNewTest($buffern['Analytic']);
                $data['Answer']=$this->getNewTest($buffern['Answer']);
                $data['Remark']=$this->getNewTest($buffern['Remark']);
                $data['TestID']=$buffern['TestID'];
                $test->data($data)->save();
            }
        }
        $test=$this->getModel('TestReal');
        $buffer=$test->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['Test']=$this->getNewTest($buffern['Test']);
                $data['Analytic']=$this->getNewTest($buffern['Analytic']);
                $data['Answer']=$this->getNewTest($buffern['Answer']);
                $data['Remark']=$this->getNewTest($buffern['Remark']);
                $data['TestID']=$buffern['TestID'];
                $test->data($data)->save();
            }
        }
        $test=$this->getModel('Testdoc');
        $buffer=$test->where('1=1')->select();
        if($buffer){
            foreach($buffer as $ii=>$buffern){
                $data=array();
                $data['DocTest']=$this->getNewTest($buffern['DocTest']);
                $data['DocAnalytic']=$this->getNewTest($buffern['DocAnalytic']);
                $data['DocAnswer']=$this->getNewTest($buffern['DocAnswer']);
                $data['DocRemark']=$this->getNewTest($buffern['DocRemark']);
                $data['TestID']=$buffern['TestID'];
                $test->data($data)->save();
            }
        }
    }
    public function getNewTest($str){
        $arr=array();
        preg_match_all('/[\s]*src=([\'|\"][^\"\']*[\"|\'])/is',$str,$arr);
        if($arr[1]){
            foreach($arr[1] as $arrn){
                $left=substr($arrn,0,1);
                $mid=substr($arrn,1,strlen($arrn)-2);
                $str=str_replace($arrn,$left.$this->getNewPath($mid,1).$left,$str);
            }
        }
        return $str;
    }
     public function getNewPath($parh,$len=0){
         $tmp_arr=explode('/',$parh);
         $tmp_len=count($tmp_arr);
         if($tmp_arr[$tmp_len-3-$len]=='docsave') $tmp_arr[$tmp_len-3-$len]='mht';
         if($tmp_arr[$tmp_len-3-$len]=='doc') $tmp_arr[$tmp_len-3-$len]='word';
         if(strlen($tmp_arr[$tmp_len-2-$len])==8){
             $tmp_arr[$tmp_len-2-$len]=date('Y/md',strtotime($tmp_arr[$tmp_len-2-$len]));
         }
         return implode('/',$tmp_arr);
     }*/
     //处理txt 一次性
     /*public function txt(){
         $str=file_get_contents(realpath('./').'/filter.txt');
         $str=str_replace('""','',$str);
         $str=str_replace('[','',$str);
         $str=str_replace(']','',$str);
         $str=str_replace(',',"\n",$str);
         $str=str_replace("\n\n","\n",$str);
         $str=str_replace("\n","\n,",$str);

         echo file_put_contents(realpath('./').'/filter1.txt',$str);
     }*/
     //清除试题属性多余数据
     /*public function testAttrNull(){
         $test=M('Test');
         $testattr=M('TestAttr');
         $buffer=$test->field('t.TestID as aa,a.TestID as bb')->table('zj_test_attr a')->join('zj_test t on t.TestID=a.TestID')->select();
         $idlist=array();
         foreach($buffer as $buffern){
             if(empty($buffern['aa'])) $idlist[]=$buffern['bb'];
         }
         $testattr->where('TestID in ('.implode(',',$idlist).')')->delete();
     }*/

     /**
     * 分值数据更正
     * @author demo 2015-8-25
     */
//    public function dataUpdate(){
//        if(isset($_POST['process'])){
//            $newer = $_POST['newer'];
//            $orginal = $_POST['orginal'];
//            $subject = $_POST['subject'];
//            $real = $_POST['real'];
//            $scores = $_POST['scores'];
//            //此处执行的始终是第一页
//            $pageNum = $this->changeSorce($orginal, $newer, $subject, $real, $scores, 1);
//            $this->setBack('success|'.$pageNum);
//        }
//        $subjects = SS("subject");
//        $url = __URL__;
//echo <<<EOF
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//<html xmlns="http://www.w3.org/1999/xhtml">
//    <head>
//        <title></title>
//        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//        <script src="/Public/plugin/jquery-1.11.1.min.js" type="text/javascript"></script>
//    </head>
//    <body>
//        <!-- 主页面开始 -->
//        <div id="main" class="main" >
//            <!-- 主体内容  -->
//            <div class="content" >
//                <div class="list" >
//                    <div id="result" class="result none"></div>
//                    <div>
//                        原始编号：
//                        <input type="text" id='orginal'/>
//                        新编号：
//                        <input type="text" id='newer'/>
//                        分值：
//                        <input type="text" id='scores' title='多个分值用"|"分开'>
//                        学科：
//                        <select id='subject'>
//                            <option value="">所有</option>
//EOF;
//                    foreach($subjects as $subject){
//                        if($subject['PID'] != 0){
//                            echo "<option value='{$subject['SubjectID']}'>{$subjects[$subject['PID']]['SubjectName']}{$subject['SubjectName']}</option>";
//                        }
//                    }
//echo <<<EOF
//                        </select>
//                        <button type='button' id='lookingFor'>更新</button>
//                    </div>
//                    <div id='msg' style="width:500px; line-height:30px; height:30px;margin-top:5px;">
//                        <span></span>
//                    </div>
//                </div>
//            </div>
//        </div>
//        <script type="text/javascript">
//            $(document).ready(function(){
//                $('#lookingFor').click(function(){
//                    if(!window.confirm('确定更新分值？')){
//                        return false;
//                    }
//                    lookingFor();
//                });
//            });
//            var countPage = 0;
//            var real = false;
//            function lookingFor(){
//                var orginal = $('#orginal').val();
//                var newer = $('#newer').val();
//                var subject = $('#subject').val();
//                var scores = $('#scores').val();
//                if(orginal === ''){
//                    alert('原始编号不能为空！');
//                    return false;
//                }
//                if(newer === ''){
//                    alert('新编号不能为空！');
//                    return false;
//                }
//                if(scores === ''){
//                    alert('分值不能为空！');
//                    return false;
//                }
//                var data = {
//                    'orginal' : orginal,
//                    'newer' : newer,
//                    'subject' : subject,
//                    'scores' : scores,
//                    'process' : 'ppppppp'
//                };
//                if(real){
//                    data['real'] = 'go';
//                }
//                $.post('{$url}-dataUpdate', data, function(result){
//                    var current = parseInt(result['data'].replace('success|', ''));
//                    if(countPage == 0){
//                        countPage = current;
//                    }
//                    if(current > 0){
//                        var msg = real ? '更新已入库试题属性。' : '更新未入库试题属性。';
//                        $('#msg span').html(msg+'总共有<font color="red">&nbsp;'+countPage+'&nbsp;</font>页数据，还剩<font color="red">&nbsp;'+current+'&nbsp;</font>页....');
//                        lookingFor();
//                    }else{
//                        $('#msg span').html('更新完成！');
//                        //更新real表数据
//                        if(!real){
//                            real = true;
//                            lookingFor();
//                        }else{
//                            real = false;
//                        }
//                        countPage = 0;
//                    }
//                });
//            }
//        </script>
//    </body>
//</html>
//EOF;
//    }
//
//    /**
//     * 分值修改
//     * @param int $orginal 原始分值
//     * @param int $new 新分值
//     * @param int $subject 学科
//     * @param string $real 当前处理的数据表。为空时为zj_test_attr，或者为zj_test_attr_real
//     * @param string $scores 分值参数 0.11|-0.01..
//     * @param int $page 页数
//     * @return int 还有多少页为处理的数据
//     * @author demo 2015-8-24
//     */
//    private function changeSorce($orginal, $new, $subject, $real, $scores, $page){
//        $tbl = empty($real) ? 'zj_test_attr' : 'zj_test_attr_real';
//        $data = $this->getUpdatedData($tbl, $orginal, $new, $subject, $scores, $page);
//        foreach($data[0] as $value){
//            $mark = $this->replace($value['Mark'],$orginal, $new, $scores);
//            $this->upgradeData($mark, $tbl, $value['TestID']);
//        }
//        return $data[1];
//    }
//
//    /**
//     * 替换内容
//     * @param string $m
//     * @param string $o 原始值
//     * @param string $n 新值
//     * @param string $s 分值
//     * @return string
//     * @author demo
//     */
//    private function replace($m, $o, $n, $s){
//        $s = str_replace('-', '\-', $s);
//        return preg_replace("/#({$o})\|({$s})#/is", '#'.$n.'|$2#', $m);
//    }
//
//    /**
//     * 更新数据
//     * @author demo
//     */
//    private function upgradeData($m, $t, $i){
//        $sql = "UPDATE `{$t}` SET `Mark`='{$m}' WHERE TestID={$i}";
//        return M()->query($sql);
//    }
//
//    /**
//     * 执行一次查询
//     * @param string $t
//     * @param int $o 原始值
//     * @param int $n 新值
//     * @param int $s 学科
//     * @param int $sc 分值
//     * @param int $p 页数
//     * @return array 下标0为数据，1为总页数
//     * @author demo 2015-8-24
//     */
//    private function getUpdatedData($t, $o, $n, $s, $sc, $p){
//        $r = 500; //每次处理500条数据
//        $sql = "SELECT COUNT(*) as c FROM {$t} WHERE ";
//        //生成分值查询语句
//        $sc = explode('|', $sc);
//        $temp = array();
//        foreach($sc as $score){
//            $temp[] = "`Mark` like '%#{$o}|{$score}#%'";
//        }
//        $sql .= '('.implode(' OR ', $temp).')';
//        if($s){
//            $sql .= ' AND SubjectID='.$s;
//        }
//        $count = M()->query($sql);
//        $count = (int)$count[0]['c'];
//        if(0 == $count){
//            array(array(), 0);
//        }
//        $l = page($count, $p, $r);
//        $l = (($l-1)*$r).','.$r;
//        $sql = str_replace('COUNT(*) as c', '`TestID`, `Mark`', $sql).' LIMIT '.$l;
//        return array(M()->query($sql), ceil($count / $r));
//    }

     /**
     * 重新更新数据，加入学科字段
     * @author demo 2015-9-17
     */
//    public function upgrade($p=1,$size=1000){
//        set_time_limit(200);
//        header('Content-type:text/html; charset=utf8');
//        if($p<=0||$size<=0||$size>2000){
//            echo '参数错误';exit;
//        }
//        $model = M();
//        $sql = 'delete from zj_user_dynamic where 1=1';
//        if($p==1) $result = $model->query($sql);
//        $sql = 'select * from zj_doc_down where DownStyle > 0 AND DownStyle < 3 ORDER BY LoadTime ASC LIMIT '.($p-1)*$size.','.$size;
//        $result = $model->query($sql);
//        if(!$result){
//            exit('数据转换完成');
//        }
//        $count = 0;
//        foreach($result as $value){
//            $style = $value['DownStyle'];
//            if($style == 1){
//                $style = 'doc';
//            }else if($style == 2){
//                $style = 'work';
//            }
//            $data['AssociateID'] = $value['DownID'];
//            $data['Title'] = $value['DocName'];
//            $data['AddTime'] = $value['LoadTime'];
//            $data['UserName'] = $value['UserName'];
//            $data['SubjectID'] = $value['SubjectID'];
//            $data['Classification'] = $style;
//            $r = $this->dbConn->insertData(
//                'UserDynamic',
//                $data
//            );
//            if($r === false){
//                $count++;
//                // echo $value['DownID'].'插入失败<br>';
//            }else{
//                // echo $value['DownID'].'插入成功<br>';
//            }
//
//        }
//        if($count===0){//一切ok，跳转
//            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,2);
//        }
//        exit('更新失败！');
//    }
    /**
     * 替换已有数据，把来源
     * @author demo
     */

//    public function changeDocSource(){
///*        $sourceName=array(
//            '试卷'=>'试卷',
//            '试题'=>'试题',
//            '同步'=>'同步',
//              );*/
//        $doc=$this->getModel("Doc");
//        $docSource=$this->getModel('DocSource');
//        /*********同步**********/
//        $docIDArr=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docIDArr1=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docSourceID=$docSource->selectData(
//            'SourceID',
//            'SourceName="同步"'
//        );
//        foreach($docIDArr1 as $i=>$iDocIdArr1){
//            $kingID1[]=$iDocIdArr1['DocID'];
//        }
//        foreach($docIDArr as $i=>$iDocIdArr){
//            $kingID[]=$iDocIdArr['DocID'];
//        }
//        $kingId=array_merge($kingID,$kingID1);
//        $kingData['SourceID']=$docSourceID[0]['SourceID'];
//        $kingID=implode(',',$kingId);
//        $updateID=$doc->updateData(
//            $kingData,
//            'DocID in('.$kingID.')'
//        );
//        if($updateID!=false){
//            echo  '【同步】数据替换成功<br>';
//        }
//        /****************同步****************/
//        $docIDArr2=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docSourceID1=$docSource->selectData(
//            'SourceID',
//            'SourceName="同步"'
//        );
//        foreach($docIDArr2 as $i=>$iDocIdArr2){
//            $kingID2[]=$iDocIdArr2['DocID'];
//        }
//        $kingData['SourceID']=$docSourceID1[0]['SourceID'];
//        $kingID1=implode(',',$kingID2);
//        $updateID1=$doc->updateData(
//            $kingData,
//            'DocID in('.$kingID1.')'
//        );
//        if($updateID1){
//            echo  '【试题调研】数据替换成功<br>';
//        }
//        /******************同步*********************/
//        $docIDArr3=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docIDArr4=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docIDArr5=$doc->SelectData(
//            'DocID',
//            'DocName like "%同步%"'
//        );
//        $docSourceID3=$docSource->selectData(
//            'SourceID',
//            'SourceName="同步"'
//        );
//        if($docIDArr3){
//            foreach($docIDArr3 as $i=>$iDocIdArr3){
//                $kingID3[]=$iDocIdArr3['DocID'];
//            }
//            $kingData['SourceID']=$docSourceID3[0]['SourceID'];
//            $kingID=implode(',',$kingID3);
//            $updateID2=$doc->updateData(
//                $kingData,
//                'DocID in('.$kingID.')'
//            );
//            if($updateID2){
//                echo  '【同步】数据替换成功<br>';
//            }
//        }
//        if($docIDArr4){
//            foreach($docIDArr4 as $i=>$iDocIdArr4){
//                $kingID4[]=$iDocIdArr4['DocID'];
//            }
//            $kingData['SourceID']=$docSourceID3[0]['SourceID'];
//            $kingID=implode(',',$kingID4);
//            $updateID4=$doc->updateData(
//                $kingData,
//                'DocID in('.$kingID.')'
//            );
//            if($updateID4){
//                echo  '【同步】数据替换成功<br>';
//            }
//        }
//        if($docIDArr5){
//            foreach($docIDArr5 as $i=>$iDocIdArr5){
//                $kingID5[]=$iDocIdArr5['DocID'];
//            }
//            $kingData['SourceID']=$docSourceID3[0]['SourceID'];
//            $kingID=implode(',',$kingID5);
//            $updateID4=$doc->updateData(
//                $kingData,
//                'DocID in('.$kingID.')'
//            );
//            if($updateID4){
//                echo  '【同步】数据替换成功<br>';
//            }
//        }
//        /********************默认：题库************************/
//        $docIDArr6=$doc->SelectData(
//            'DocID',
//            'SourceID=0'
//        );
//        foreach($docIDArr6 as $i=>$iDocIdArr6){
//            $kingId6[]=$iDocIdArr6['DocID'];
//        }
//        $weilaidocSourceID=$docSource->selectData(
//            'SourceID',
//            'SourceName="题库"'
//        );
//        $kingData['SourceID']=$weilaidocSourceID[0]['SourceID'];
//        $kingID6=implode(',',$kingId6);
//        $updateID2=$doc->updateData(
//            $kingData,
//            'DocID in('.$kingID6.')'
//        );
//        if($updateID2){
//            echo  '默认【题库】数据替换成功<br>';
//        }
//    }
    /**
     *  doc_down用户资源名称匹配替换
     * @param int $p
     * @param int $size
     * @author demo
     */
/*    public function fullDocName($p=1,$size=100){
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>500){
            echo '参数错误';
        }
        //查询
        $db = $this->dbConn->field('d.DownID,d.DocName,s.SchoolName')
            ->table($this->dbConn->formatTable('DocDown').' d')
            ->join($this->dbConn->formatTable('User').' U on d.UserName=U.UserName')
            ->join($this->dbConn->formatTable('School').' s on s.SchoolID=U.SchoolID')
            ->where('d.DownStyle=1')
            ->group('d.DownID')
            ->limit(($p-1)*$size,$size)
            ->select();
        //循环更新
        if(!$db){
            exit('数据转换完成');
        }
        $wrongAmount = 0;
        foreach($db as $iDb){
            $docName=$iDb['DocName'];
            if($iDb['SchoolName']){
                $docName=str_replace('xxx学校',$iDb['SchoolName'],$iDb['DocName']);
                $update = $this->getModel('DocDown')->updateData(
                    ['DocName'=>$docName],
                    ['CollectID'=>$iDb['DownID']]
                );
                if($update===false){
                    echo '文档名称：'.$iDb['DocName'].'更新错误';
                    $wrongAmount += 1;
                }else{
                    echo '<br>success DownID='.$iDb['DownId'].' ['.$iDb['DocName'].'] set'.$docName.'<br>';
                }
            }
        }
        if($wrongAmount===0){//一切ok，跳转
            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
        }
    }*/
    /**
     *  doc_down用户资源名称匹配替换
     * @param int $p
     * @param int $size
     * @author demo
     */
/*    public function fullDocSaveName($p=1,$size=100){
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>500){
            echo '参数错误';
        }
        //查询
        $db = $this->dbConn->field('d.SaveID,d.SaveName,d.CookieStr,s.SchoolName')
            ->table($this->dbConn->formatTable('DocSave').' d')
            ->join($this->dbConn->formatTable('User').' U on d.UserName=U.UserName')
            ->join($this->dbConn->formatTable('School').' s on s.SchoolID=U.SchoolID')
            ->where('1=1')
            ->group('d.SaveID')
            ->limit(($p-1)*$size,$size)
            ->select();
        //循环更新
        if(!$db){
            exit('数据转换完成');
        }
        $wrongAmount = 0;
        foreach($db as $iDb){
            $saveName=$iDb['SchoolName'];
            $cookieStr=$iDb['CookieStr'];
            if($iDb['SchoolName']){
                $saveName=str_replace('xxx学校',$iDb['SchoolName'],$iDb['SaveName']);
                $cookieStr=str_replace($iDb['SaveName'],$saveName,$iDb['CookieStr']);
                $update = $this->getModel('DocDown')->updateData(
                    ['DocName'=>$saveName,'CookieStr'=>$cookieStr],
                    ['SaveID'=>$iDb['SaveID']]
                );
                if($update===false){
                    echo '文档名称：'.$iDb['SaveName'].'更新错误';
                    $wrongAmount += 1;
                }else{
                    echo '<br>success SaveID='.$iDb['SaveID'].' ['.$iDb['SaveName'].'] set'.$saveName.'<br>';
                }
            }
        }
        if($wrongAmount===0){//一切ok，跳转
            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
        }
    }*/

    /**
     * 文档地区ID内容完善
     * @author demo
     */
/*    public function fullDocAreaID($p=1,$size=1000){
        //Doc所有
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>500){
            echo '参数错误';
        }
        $limit=($p-1)*$size.','.$size;
        $allDocID=$this->dbConn->selectData(
            'Doc',
            'DocID',
            '1=1',
            'DocID desc',
            $limit
        );
        foreach($allDocID as $i=>$iAllDocId){
            $DocList[]=$iAllDocId['DocID'];
        }
        $docListStr=implode(',',$DocList);
        if(!$docListStr){
            exit('数据转换完成');
        }
        $wrongAmount = 0;
        //DocArea 所有
        $areaDocID=$this->dbConn->selectData(
            'DocArea',
            'DocID',
            'DocID in('.$docListStr.')'
        );
        foreach($areaDocID as $i=>$iAreaDocID){
            $areaIDList[]=$iAreaDocID['DocID'];
        }
        foreach($DocList as $i=>$iDocList){
            if(!in_array($DocList[$i],$areaIDList)){
                $data['DocID']=$DocList[$i];
                $data['AreaID']=0;
                $result=$this->getModel('DocArea')->insertData(
                    $data
                );
                if($result===false){
                    echo '文档名称：'.$DocList[$i].'录入错误';
                    $wrongAmount += 1;
                }else{
                    echo '<br>success DocID='.$DocList[$i].'<br>';
                }
            }
            if($wrongAmount===0){//一切ok，跳转
                redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
            }
        }
    }*/

    /**
     * 修改个人用户在集体用户下的数据 2015-10-15
     * @author demo
     */
//     public function upgradeTeamToPersonal(){
//         $sql = "UPDATE `zj_user_group` SET GroupID=42, LastTime=AddTime+(30*60*60*24) WHERE GroupID=5 AND GroupName=1";
//         $result = M('')->query($sql);
//         if($result === false){
//             exit('failure');
//         }
//         exit('success');
//     }
    /**
     * 根据记录，状态信息，完成zj_pay 表数据记录
     * 提取审核通过校本题库试题
     */
/*    public function addPayData1(){
        $this->getModel('Pay')->deleteData(
            'PayName="校本题库试题"'
        );
        $this->getModel('Pay')->deleteData(
            'PayName="校本题库审核试题"'
        );
        //对数据表zj_custom_test_task_log提取审核通过和删除试题 到支出表
       $okCustom=$this->dbConn->field('a.Point,a.TestID,a.AddTime,a.Description,c.TestID,c.UserID')
                                ->table('zj_custom_test_task_log a')
                                ->join('zj_custom_test_attr c on c.TestID=a.TestID')
                                ->where('a.status="审核通过"')
                                ->select();
        foreach($okCustom as $i=>$iOkCustom){
            $userName=explode('审核',$okCustom[$i]['Description']);
            $userID=$this->getModel('User')->getInfoByName($userName[0])[0]['UserID'];
            $okPayData['PayMoney']=$okCustom[$i]['Point'];
            $okPayData['UserID']=$okCustom[$i]['UserID'];
            $okPayData['PayName']='校本题库试题';
            $okPayData['AddTime']=time();
            $okPayData['PayDesc']="管理员【".$userID."】审核通过教师[".$okCustom[$i]['UserID']."]上传的试题ID【".$okCustom[$i]['TestID']."】";
            $result=$this->getModel('Pay')->addPayLog($okPayData);
            echo $result.'<br>';
        }
    }*/

    /**
     * 生成全站用户完成解析试题任务，得到的分成金额
     * @author demo
     */
/*    public function addPayData2($p=1,$size=1000){
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>1100){
            echo '参数错误';
        }
        $limit=($p-1)*$size.','.$size;
        //对数据表zj_doc_file数据替换 DocID !=0  TASK_DOC_FILE=0.6
        $docFile=$this->getModel('DocFile')->selectData(
            'DocID,SubjectID,UserName',
            'DocID<>0 and PayStatus=0',
            'DocID DESC',
            $limit
        );
        if(!$docFile){
            exit('替换完成');
        }
        $wrongAmount = 0;
        foreach($docFile as $i=>$iDocFile){
            $testArr=$this->getModel('TestAttrReal')->selectData(
                'testID',
                'DocID='.$docFile[$i]['DocID'].''
            );
            if($testArr){
                $userID=$this->getModel('User')->getInfoByName($docFile[$i]['UserName'])[0]['UserID'];
                $result=$this->getModel('Pay')->addPayBySubject($testArr,$userID,$docFile[$i]['DocID'],$docFile[0]['SubjectID']);
                if($result===false){
                    echo $this->dbConn->_sql().'<BR>';
                    $wrongAmount += 1;
                }else{
                    echo '<br>success '.$result.'<br>';
                }
            }
        }
        if($wrongAmount===0){//一切ok，跳转
            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
        }
    }*/

    /**
     * 生成全站用户完成试题标引任务，得到的分成金额
     * @author demo
     */
/*    public function addPayData3($p=1,$size=300){
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>1100){
            echo '参数错误';
        }
        $limit=($p-1)*$size.','.$size;
        //对数据zj_teacher_work status=2        TASK_TEACHER_WORK=0.4
        //Doc所有
        $bufferWork=$this->dbConn->field('a.WorkID,a.SubjectID,a.UserName,a.Admin,b.DocID')
            ->table('zj_teacher_work a')
            ->join('zj_teacher_work_list b on a.WorkID=b.WorkID')
            ->where('a.Status=2')
            ->limit($limit)
            ->select();
        $wrongAmount = 0;
        if(empty($bufferWork)){
            exit('替换完成');
        }
        foreach($bufferWork as $i=>$iBuffer){
            //统计这个文档下的试题数量
            $testArr=$this->dbConn->selectData(
                'TestAttrReal',
                'TestID',
                'DocID in ('.$bufferWork[$i]['DocID'].')'
            );
            //必须有入库试题
            if(!empty($testArr)){
                foreach($testArr as $i=>$iTestArr){
                    $testStr[]=$testArr[$i]['TestID'];
                }
                //对完成标引任务的老师积累
                //获取标引老师的UserID
                $userID=$this->getModel('User')->getInfoByName($bufferWork[$i]['UserName'])[0]['UserID'];
                if(!empty($userID)){
                    $testIdStr=implode(',',$testStr);
                    $totalTest=count($testArr);
                    $payData['UserID']=$userID;
                    $payData['PayName']='试题标引';
                    $payData['PayMoney']=C('WLN_TAG_TEST_MONEY')*$totalTest;
                    $payData['PayDesc']="教师[".$bufferWork[$i]['Admin']."]审核通过了作者ID为[".$bufferWork[$i]['UserName']."]文档ID为【".$bufferWork[$i]['DocID']."】,试题:【".$testIdStr."】";
                    $payData['AddTime']=time();
                    $result=$this->getModel('Pay')->addPayLog($payData);
                    if($result===false){
                        echo $this->dbConn->_sql().'<BR>';
                        $wrongAmount += 1;
                    }else{
                        echo '<br>success '.$result.'<br>';
                    }
                }
            }else{
                echo '文档【'.$bufferWork[$i]['DocID'].'】对应的试题ID为空！<br>';
            }
        }
        if($wrongAmount===0){//一切ok，跳转
            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
        }
    }*/

    /**
     * 生成全站用户完成试题审核任务，得到的分成金额
     * @author demo
     */
/*    public function addPayData4($p=1,$size=500){
        echo '<meta charset="utf-8">';
        if($p<=0||$size<=0||$size>1100){
            echo '参数错误';
        }
        $limit=($p-1)*$size.','.$size;
        //对数据zj_teacher_work_check status=2  TASK_TEACHER_WORK_CHECK=0.2
        $buffer=$this->dbConn->field('a.WorkID,a.SubjectID,a.UserName,a.Admin,a.WCID,b.DocID')
                            ->table('zj_teacher_work_check a')
                            ->join('zj_teacher_work_list b on a.WorkID=b.WorkID')
                            ->where('a.Status=2')
                            ->limit($limit)
                            ->select();
        $wrongAmount = 0;
        if(empty($buffer)){
            exit('替换完成');
        }
        foreach($buffer as $i=>$iBuffer){
            //统计这个文档下的试题数量
            $testArr=$this->dbConn->selectData(
                'TestAttrReal',
                'TestID',
                'DocID in ('.$buffer[$i]['DocID'].')'
            );
            //必须有入库试题
            if(!empty($testArr)){
                foreach($testArr as $i=>$iTestArr){
                    $testStr[]=$testArr[$i]['TestID'];
                }
                $userID=$this->getModel('User')->getInfoByName($buffer[$i]['UserName'])[0]['UserID'];
                if(!empty($userID)){
                    $testIdStr=implode(',',$testStr);
                    $totalTest=count($testArr);
                    $payData['UserID']=$userID;
                    $payData['PayName']='试题审核';
                    $payData['PayMoney']=C('WLN_TAG_TEST_MONEY')*$totalTest;
                    $payData['PayDesc']="教师[".$buffer[$i]['Admin']."]审核通过了作者ID为[".$buffer[$i]['UserName']."]任务ID为【".$buffer[$i]['WorkID']."】,试题:【".$testIdStr."】";
                    $payData['AddTime']=time();
                    $result=$this->getModel('Pay')->addPayLog($payData);
                    if($result===false){
                        echo $this->dbConn->_sql().'<BR>';
                        $wrongAmount += 1;
                    }else{
                        echo '<br>success '.$result.'<br>';
                    }
                }
            }else{
                echo '文档【'.$buffer[$i]['DocID'].'】对应的试题ID为空！<br>';
            }
        }
        if($wrongAmount===0){//一切ok，跳转
            redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
        }
    }*/
    /**
     * 更新模板组卷跟导学案的更新时间同步
     * @author demo
     */
/*    public function updateTplTime(){
        //模板组卷
        $dirTemplate=$this->getModel('DirTemplate');
        $list=$dirTemplate->selectData(
            'TempID,AddTime',
            'UpdateTime=0'
        );
        foreach($list as $i=>$iList){
            $data['UpdateTime']=$list[$i]['AddTime'];
            $dirTemplate->updateData(
                $data,
                'TempID='.$list[$i]['TempID']
            );
              }
        //导学案数据
        $caseTpl=$this->getModel('CaseTpl');
        $caseList=$caseTpl->selectData(
            'TplID,AddTime',
            'UpdateTime=0'
        );
        foreach($caseList as $i=>$iCaseList){
            $caseData['UpdateTime']=$caseList[$i]['AddTime'];
            $caseTpl->updateData(
                $caseData,
                'TplID='.$caseList[$i]['TplID']
            );
        }
    }*/

    /**
     * 整理模板组卷中的名称问题
     * @author demo
     */
/*    public function changeSchoolNameForTpl(){
        //获取系统模板
        $tplList=$this->dbConn->selectData('DirTemplate','*','IfDefault=0');
        foreach($tplList as $i=>$iTplList){
            $tplTitle=unserialize($tplList[$i]['Content']);
            if(strpos($tplTitle['maintitle'],"###")!=false){
                $tplTitle['maintitle']=str_replace('###','xxx',$tplTitle['maintitle']);
            }elseif(strpos($tplTitle['maintitle'],"####")!=false){
                $tplTitle['maintitle']=str_replace('####','xxx',$tplTitle['maintitle']);
            }
            $data['Content']=serialize($tplTitle);
            $result=$this->getModel('DirTemplate')->updateData(
                $data,
                'TempID='.$tplList[$i]['TempID']
            );
        }
    }*/

//    /**
//     * 把用户最后登陆的IP作为注册IP
//     * @author demo
//     */
//    public function upDateUserRegisterLog($p=1,$size=1000){
//            //userIP所有
//            echo '<meta charset="utf-8">';
//            if($p<=0||$size<=0||$size>500){
//                echo '参数错误';
//            }
//            $limit=($p-1)*$size.','.$size;
//            $userList=$this->getModel('User')->selectData(
//                'UserID,LastIP',
//                '1=1',
//                'UserID desc',
//                $limit
//            );
//            if(!$userList){
//                exit('数据转换完成');
//            }
//            $wrongAmount = 0;
//            //DocArea 所有
//            foreach($userList as $i=>$iUserList){
//                //查看该用户是否存已存在注册记录
//                $thisUserMsg=$this->getModel('RegisterLog')->selectData(
//                    'RegLogID',
//                    'UserID='.$userList[$i]['UserID']
//                );
//                if(empty($thisUserMsg)){
//                    if($userList[$i]['LastIP']!=''){
//                        $thisIPID=$this->getModel('UserIp')->selectData(
//                            'IPID',
//                            'IPAddress='.ip2long($userList[$i]['LastIP'])
//                        );
//                        if($thisIPID){
//                            $insertData['IPID']=$thisIPID[0]['IPID'];
//                            $insertData['UserID']=$userList[$i]['UserID'];
//                            $insertData['RegTime']=time();
//                            $result=$this->getModel('RegisterLog')->insertData(
//                                $insertData
//                            );
//                            if($result===false){
//                                echo '用户ID：'.$userList[$i]['UserID'].'录入错误';
//                                $wrongAmount += 1;
//                            }else{
//                                echo '<br>success UserID='.$userList[$i]['UserID'].'<br>';
//                            }
//                        }
//                    }
//                }
//            }
//            if($wrongAmount===0){//一切ok，跳转
//                redirect(__ACTION__.'?p='.($p+1).'&size='.$size,1);
//            }
//        }


        /**
         * 更新任务大厅
         * @author demo 2015-11-17
         */
    // public function tasksUpgrade(){
    //     header('Content-type:text/html;charset=utf8');
    //     $s = strtotime('2015-11-13 13:30:00');
    //     $e = strtotime('2015-11-17');
    //     $tables = array(
    //         'zj_class_list'=>'Creator,LoadTime,Home-MyClass-createClass',
    //         'zj_user_work'=>'UserName,LoadTime,Home-Work-assignLeavedUserWork',
    //         'zj_custom_test_attr'=>'UserID,AddTime,Home-CustomTestStore-save',
    //         'zj_custom_doc_upload'=>'UserID,AddTime,Home-CustomTestStore-docUpload',
    //         'zj_case_custom_lore_attr'=>'UserName,AddTime,Home-Case-saveLore',
    //         'zj_doc_down'=>'UserID,LoadTime,Home-Case-create,DownStyle=4'
    //     );
    //     foreach($tables as $t=>$f){
    //         $f = explode(',', $f);
    //         $group = '';
    //         if($f[0] != 'UserID'){
    //             $sql = "SELECT count(u.UserID) as num ,u.UserID FROM {$t} t LEFT JOIN `zj_user` u ON u.UserName=t.{$f[0]}";
    //             $group = 'u.UserID';
    //         }else{
    //             $sql = "SELECT count($f[0]) as num, UserID FROM {$t} ";
    //             $group = 'UserID';
    //         }
    //         $sql .= " WHERE {$f[1]} BETWEEN {$s} AND {$e}";
    //         if(isset($f[3])){
    //             $sql .= ' AND '. $f[3];
    //         }
    //         $sql .= " GROUP BY {$group};";
    //         echo ($sql).'<br>';
    //         $result = (M()->query($sql));
    //         $temp = array();
    //         foreach($result as $key=>$value){
    //             $temp[$value['UserID']] = $value['num'];
    //         }
    //         unset($result);
    //         $userlist = array_keys($temp);
    //         $userlist = array_filter($userlist);
    //         $userlist = implode(',', $userlist);
    //         if(!empty($userlist)){
    //             echo "当前操作表：{$t}<br>";
    //             $sql = "SELECT MHLID,r.UserID,l.Status FROM `zj_mission_hall_log` l LEFT JOIN `zj_mission_hall_records` r ON r.MHRID=l.MHRID LEFT JOIN `zj_mission_hall_modules` m ON m.MHTID=r.MHTID WHERE r.UserID IN({$userlist}) AND (l.`Status`=1 OR l.`Status`=4) AND m.JumpUrl='{$f[2]}' ORDER BY l.AddTime DESC;";
    //             unset($userlist);
    //             // echo $sql.'<br>';
    //             $result = M()->query($sql);
    //             $userlist = array();
    //             foreach($result as $value){
    //                 if(4 == $value['Status']){
    //                     if(!isset($userlist[$value['UserID']]['num'])){
    //                         $userlist[$value['UserID']]['num'] = 1;
    //                     }else{
    //                         $userlist[$value['UserID']]['num'] = ++$userlist[$value['UserID']]['num'];
    //                     }
    //                 }else{
    //                     $userlist[$value['UserID']]['ids'][] = $value['MHLID'];
    //                 }
    //             }
    //             $ids = [];
    //             foreach($temp as $key=>$value){
    //                 if(!isset($userlist[$key])){
    //                     continue;
    //                 }
    //                 $value -= $userlist[$key]['num'];
    //                 for($i=0; $i<$value; $i++){
    //                     if(isset($userlist[$key]['ids'][$i]))
    //                         $ids[] = $userlist[$key]['ids'][$i];
    //                 }
    //             }
    //             $ids = implode(',', $ids);
    //             if(!empty($ids)){
    //                 echo "更新id：{$ids}<br>";
    //                 $sql = "UPDATE `zj_mission_hall_log` SET `Status`=4 WHERE MHLID IN ({$ids})";
    //                 $r = M()->query($sql);
    //             }
    //             echo "更新{$t}完成<br>";
    //             echo "----------------------------<br>";
    //         }
    //     }
    //     exit('全部更新完成');
    // }

//    /**
//     * 生成个人组卷次数的随机数据
//     * @author demo
//     */
//    public function testPaperLogInsert(){
//        header('Content-type:text/html;charset=utf-8');
//        $page = (int)$_GET['p'];
//        if(!empty($page)){
//            $prepage = 1000;
//            $count = $this->getModel('User')->findData( 'COUNT(UserID) as c');
//            if(empty($count)){
//                $count = 0;
//            }else{
//                $count = $count['c'];
//            }
//            $count = ceil($count/$prepage);
//            if($page <= $count){
//                $m = new \Common\Model\StatisticsHomeModel();
//                $m->test($page, $prepage);
//                if($page >= $count){
//                    $count = $this->getModel('TestpaperCenterLog')->findData( 'COUNT(TCID) as c', '1=1');
//                    $sum = $this->getModel('User')->findData('SUM(ComTimes) as s', '1=1');
//                    $str = "执行完成，用户总共组卷{$sum['s']}次，共产生记录{$count['c']}条！";
//                    exit($str);
//                }
//                $page++;
//                echo ("<script>window.location.href='". U("Manage/Index/testPaperLogInsert",array('p'=>$page)). "'</script>");
//            }else{
//                exit();
//            }
//        }else{
//            $page = 1;
//            header('location:'.U("Manage/Index/testPaperLogInsert",array('p'=>$page)));
//        }
//    }

    /**
     * ##############
     * 特殊操作
     * ##############
     */

//    /**
//     * 临时方法:替换数据库中的URL格式 - => /
//     * @Wanging: 操作前请备份数据库,使用后删除
//     * @author demo
//     */
//    public function changeUrlFormat(){
//        //设置最大执行时间
//        if(function_exists('set_time_limit')){
//            set_time_limit(0);
//        }
//
//        $apiDb = $this->getModel('ApiDb');
//        $sql   = '';//初始化sql变量
//
//        //更新MissionHallModules
//        $sql  = "UPDATE `zj_mission_hall_modules` set `JumpUrl` = REPLACE(`JumpUrl`,'-','/')";
//        if($apiDb->execute($sql)!==false){
//            echo 'MissionHallModules执行成功...';
//        }else{
//            echo 'MissionHallModules执行失败';
//        }
//        echo "<br />";
//
//        //更新MissionHallTasks
//        $sql  = "UPDATE `zj_mission_hall_tasks` set `Url` = REPLACE(`Url`,'-','/')";
//        if($apiDb->execute($sql)!==false){
//            echo 'MissionHallTasks执行成功...';
//        }else{
//            echo 'MissionHallTasks执行失败';
//        }
//        echo "<br />";
//
//        //更新CoreSystem
//        $sql  = "UPDATE `zj_core_system` set `Http` = REPLACE(`Http`,'-','/')";
//        if($apiDb->execute($sql)!==false){
//            echo 'CoreSystem执行成功...';
//        }else{
//            echo 'CoreSystem执行失败';
//        }
//        echo "<br />";
//        //更新zj_my_tag里的URL 只能执行一次
//        //特殊情况检查是否已经执行过该程序
//        $lockData = $apiDb->selectData(
//            'MyTag',
//            'Content',
//            ['Type'=>'knowMe','TagName'=>'knowMeNav']
//        );
//
//        $lockData = $lockData[0]['Content'];
//        if($lockData){
//            if(preg_match('/Index\/About\//',$lockData)) {
//                exit('zj_my_tag已经执行');
//            }
//            $sql = "UPDATE `zj_my_tag` set `Content` = REPLACE(`Content`,'About/','Index/About/');";
//            if ($apiDb->execute($sql) !== false) {
//                echo 'zj_my_tag执行成功...';
//            } else {
//                echo 'zj_my_tag执行成功...';
//            }
//        }else{
//            exit('zj_my_tag危险操作!');
//        }
//    }

    //---------------------------------------------------------
//    /**
//     * 替换无效的错误码
//     * @author demo 16-1-7
//     */
//    public function replaceInvalidErrorCode(){
//        $invalidCode = $_POST['old'];
//        if(is_string($invalidCode)){
//            $invalidCode = array_unique(explode("\r\n", preg_replace('/^\s+/m', '', $invalidCode)));
//        }
//        $newer = $_POST['new'];
//        if(is_string($newer)){
//            $newer = array_unique(explode("\r\n", preg_replace('/^\s+/m', '', $newer)));
//        }
//        header('Content-type:text/html;charset=utf8');
//        echo '<html><head></head><body>';
//        $replace = $_POST['replace'];
//        if(!$replace){
//            echo ('<form action="'.__URL__.'/replaceInvalidErrorCode" method="post"><form><textarea name="old" rows=10>'.implode("\r\n", $invalidCode).'</textarea><button type="button" onclick="document.forms[0].submit();">预览</button>&nbsp;<button type="button" onclick=\'if(!window.confirm("确认替换？")){return false;} document.forms[1].submit();\'>替换</button></form>');
//            $invalidCode = array_unique($invalidCode);
//            $newer = array_unique($newer);
//        }
//        $temp = array_filter($newer);
//        if($replace && (empty($invalidCode) || empty($temp))){
//            exit('替换错误码为空！');
//        }
//        unset($temp);
//        if($replace){
//            foreach($invalidCode as $k=>$v){
//                if(empty($newer[$k]) || !is_integer((int)$newer[$k])){
//                    unset($invalidCode[$k], $newer[$k]);
//                }
//            }
//        }
//
//        if($replace && count($invalidCode) != count($newer)){
//            exit('旧错误码与新错误码的数量不同。');
//        }
//        $dirs = glob(APP_PATH.'*', GLOB_ONLYDIR);
//        $invalidCodeTmp = $invalidCode;
//        foreach($invalidCodeTmp as $key=>$value){
//            $invalidCodeTmp[$key] = "<font color='red'><strong>###########{$value}</strong></font>";
//        }
//        $count = 0;
//        foreach($dirs as $dir){
//            ob_start();
//            $html = "";
//            //替换控制器
//            $files = array_merge((array)glob($dir.'/Wln/*.php'), (array)glob($dir.'/Controller/*.php'),(array)glob($dir.'/Model/*.php'));
//            foreach($files as $file){
//                $content = $this->replacePreview($file, $invalidCode, $invalidCodeTmp);
//                if(!empty($content)){
//                    $count++;
//                    if($replace){
//                        $this->replaceFile($file, $invalidCode, $newer);
//                    }
//                    $content = "<dl><dt>---------{$file}--------</dt>".$content;
//                }
//                $html .= $content;
//            }
//            echo $html;
//            ob_end_flush();
//        }
//        if($replace){
//            exit("<strong>共替换{$count}个文件</strong><br></body>");
//        }else{
//echo <<<EOF
//<script>
//        var list = {};
//        var dd = document.getElementsByTagName('dd');
//        for(var i=0; i<dd.length; i++){
//            var html = dd[i].innerHTML;
//            var warpline = html.split('<br>');
//            for(var j=0; j<warpline.length; j++){
//                if(warpline[j]){
//                    var matches = warpline[j].match(/(<font color=['|"]red['|"]><strong>)(\d+[X]*\d+)(<\/strong><\/font>)/ig);
//                    for(var k=0; k<matches.length; k++){
//                        var errCode = matches[k].replace(/<font color=['|"]red['|"]><strong>|<\/strong><\/font>/g, '');
//                        var link = dd[i].previousSibling.innerHTML;
//                        if(!list[errCode]){
//                            list[errCode] = {};
//                        }
//                        if(!list[errCode][link]){
//                            list[errCode][link] = [];
//                        }
//                        warpline[j] = warpline[j].replace(/&lt;/g, '<')
//                                                    .replace(/&gt;/g, '>')
//                                                    .replace(/&nbsp;/g, '   ')
//                                                    .replace(/<br>/g, '\\r\\n');
//                        list[errCode][link].push(warpline[j]);
//                    }
//                }
//            }
//        }
//        var dl = document.getElementsByTagName('dl');
//        var i = dl.length-1;
//        for(; i>=0; i--){
//            dl[i].parentNode.removeChild(dl[i]);
//        }
//        var br = document.getElementsByTagName('br');
//        var i = br.length-1;
//        for(; i>=0; i--){
//            br[i].parentNode.removeChild(br[i]);
//        }
//        var form = document.createElement('form');
//        form.action = document.forms[0].action;
//        form.target = '_blank';
//        form.method = 'post';
//        var input = document.createElement('input');
//        input.type = 'hidden';
//        input.name = 'replace';
//        input.value = 'go';
//        form.appendChild(input);
//        for(var data in list){
//            var dl = document.createElement('dl');
//            var dt = document.createElement('dt');
//            var dtv = document.createTextNode(data+'  替换为：');
//            dt.appendChild(dtv);
//            var input = document.createElement('input');
//            input.name = 'new[]';
//            dt.appendChild(input);
//            var input = document.createElement('input');
//            input.name = 'old[]';
//            input.type = 'hidden';
//            input.value = data;
//            dt.appendChild(input);
//            dl.appendChild(dt);
//            for(var data1 in list[data]){
//                var dd = document.createElement('dd');
//                var dt1 = document.createElement('dt');
//                var font = document.createElement('font');
//                font.color='#56A9FB';
//                font.style.fontWeight='bold';
//                var dt1v = document.createTextNode(data1);
//                font.appendChild(dt1v);
//                dt1.appendChild(font);
//                dd.appendChild(dt1);
//                dl.appendChild(dd);
//                for(var data2 in list[data][data1]){
//                    var dd1 = document.createElement('dd');
//                    var dt2 = document.createElement('dd');
//                    var html = list[data][data1][data2];
//                    var arr =  html.split(/<font color=['|"]red['|"]><strong>/g);
//                    for(var i=0; i<arr.length; i++){
//                        if(/<\/strong><\/font>/.test(arr[i])){
//                            var arr1 = arr[i].split(/<\/strong><\/font>/g);
//                            var font = document.createElement('font');
//                            var strong = document.createElement('strong');
//                            strong.appendChild(document.createTextNode(arr1[0]));
//                            font.style.color='red';
//                            font.appendChild(strong);
//                            dt2.appendChild(font);
//                            dt2.appendChild(document.createTextNode(arr1[1]));
//                        }else{
//                            dt2.appendChild(document.createTextNode(arr[i]));
//                        }
//                    }
//                    dd1.appendChild(dt2);
//                    dt1.appendChild(dd1);
//                }
//            }
//            form.appendChild(dl);
//        }
//        document.body.appendChild(form);
//    </script></body>
//EOF;
//        }
//            // echo $html;
//    }
//
//    private function replaceFile($file, $invalidCode, $newer){
//        $content = file_get_contents($file);
//        $sys = fopen($file, 'w');
//        $content = str_replace($invalidCode, $newer, $content);
//        fwrite($sys, $content);
//        fclose($sys);
//    }
//
//    private function replacePreview($file, $invalidCode, $invalidCodeTmp){
//        $html = '';
//        $content = file_get_contents($file);
//        $errorCodeList = $this->matchClassErrorCode($content);
//        $isContain = false;
//        if(empty($errorCodeList[0])){
//            return $html;
//        }
//        $errorCodeList = array_unique($errorCodeList[0]);
//        if(count($invalidCode) >= count($errorCodeList)){
//            if(array_intersect($invalidCode, $errorCodeList)){
//                $isContain = true;
//            }
//        }else{
//            if(array_intersect($errorCodeList, $invalidCode)){
//                $isContain = true;
//            }
//        }
//        if($isContain){
//            $content = htmlentities($content);
//            $content = (str_replace(array("\r\n","\r", "\n"), '<br>', $content));
//            $content = (str_replace(' ', '&nbsp;', $content));
//            $content = (str_replace($invalidCode, $invalidCodeTmp, $content));
//            preg_match_all('/###########\d+/im', $content, $matches, PREG_OFFSET_CAPTURE);
//            $offsets = $this->getWarppingLinesOffset($content);
//            $leng = count($offsets);
//            $lines = array();
//            foreach($matches[0] as $value){
//                $end = 0;
//                for($i=0; $i<$leng; $i++){
//                    if($offsets[$i] > $value[1]){
//                        $end = $offsets[$i];
//                        break;
//                    }
//                }
//                $start = 0;
//                if($i > 0){
//                    $start = $offsets[$i-1]+4;
//                }
//                $end = $end - $start; //去除br标签的长度
//                $i++;
//                $lines[] = "第{$i}行：".str_replace('###########', '', mb_substr($content, $start, $end+4));
//            }
//            $content = implode('', $lines);
//            $html .= '<dd>'.$content.'</dd></dl><br/>';
//        }
//        return $html;
//    }
//
//    private function getWarppingLinesOffset($content){
//        preg_match_all('/<br>/m', $content, $matches, PREG_OFFSET_CAPTURE);
//        $val = array();
//        foreach($matches[0] as $value){
//            $val[] = $value[1];
//        }
//        return $val;
//    }
//
//    /**
//     * 查询无效的错误码
//     * @author demo 16-1-7
//     */
//    public function searchInvalidErrorCode(){
//        $dir = $_GET['dir'];
//        header('Content-type:text/html;charset=utf8');
//        $dirs = glob(APP_PATH.'*', GLOB_ONLYDIR);
//        $html = '<option>请选择</option>';
//        foreach($dirs as $d){
//            $name = trim(strrchr ($d, '/'), '/');
//            if('runtime' != strtolower($name))
//                $html .= "<option value='{$name}'>{$d}</option>";
//        }
//        $html = str_replace("<option value='{$dir}'", "<option value='{$dir}' selected='selected'", $html);
//        echo ("<select onchange=\"window.location.href=window.location.href.replace(/\/dir\/.*$/g, '')+'/dir/'+this.value;\">{$html}</select>&nbsp; <a href='".__URL__."/replaceInvalidErrorCode' target='_blank'>替换/预览</a>");
//        $common = $this->matchConfErrorCode(file_get_contents(APP_PATH.'Common/Conf/config.php'));
//        $dir = APP_PATH.$dir.'/';
//        $conf = $this->matchConfErrorCode(file_get_contents($dir.'Conf/config.php'));
//        $files = array_merge((array)glob($dir.'Controller/*.php'), (array)glob($dir.'Wln/*.php'));
//        $html = $this->getHtml($files, $common.','.$conf, $dir);
//        if(empty($html)){
//            $html = '无控制器';
//        }
//        echo '<dl><dt>控制器</dt><dd>'.$html.'</dd></dl>';
//        $files = glob($dir.'Model/*.php');
//        $html = $this->getHtml($files, $common, $dir);
//        if(empty($html)){
//            $html = '无模型';
//        }
//        echo '<dl><dt>模型</dt><dd>'.$html.'</dd></dl>';
//    }
//
//    private function getHtml($files, $errorCode, $mod){
//        $html = '';
//        $errlist = array();
//        foreach($files as $fileName){
//            $html .= "<dt>{$fileName}</dt>";
//            $content = $this->matchClassErrorCode(file_get_contents($fileName));
//            $content = $this->checkErrorCode($errorCode, $content);
//            if(empty($content)){
//                $str = '无错误码';
//            }else{
//                $str = $content[0];
//                $list = $content[1];
//                $errlist = array_merge($errlist, $list);
//            }
//            $html .= "<dd>{$str}</dd>";
//        }
//        if(empty($html)){
//            return $html;
//        }
//        $errlist = array_unique($errlist);
//        if(empty($errlist)){
//            return '<dl>'.$html.'</dl>';
//        }
//        return '<dl>'.$this->generateEditor($errlist, $mod).$html.'</dl>';
//    }
//
//    private function generateEditor($errlist, $mod){
//        // $html = '<dt>编辑存在问题的错误码</dt><dd><form><input type="hidden" dir="'.$mod.'"/><textarea name="old" rows=5>%s</textarea>替换为<textarea name="new" rows=5></textarea><button type="submit">替换</button></form></dd>';
//        return '<dt>存在问题的错误码</dt><dd>'.implode("<br>", $errlist).'</dd>';
//    }
//
//    private function checkErrorCode($errorCode, $classErrorCode){
//        $errorCode = explode(',', $errorCode);
//        $html = '';
//        $errlist = array();
//        foreach($classErrorCode[0] as $code){
//            $str = '';
//            if(in_array('ERROR_'.$code, $errorCode) === false){
//                $errlist[] = $code;
//                $str .= '<font color="red">'.$code.'不存在</font>';
//            }else{
//                $str .= $code;
//            }
//            $html .= "<dd>{$str}</dd>";
//        }
//        if($html){
//            $html = '<dt>共发现'.count($classErrorCode[0]).'个错误码：<dt>'.$html;
//            return array('<dl>'.$html.'</dl>', array_unique($errlist));
//        }
//        return $html;
//    }
//
//    private function matchClassErrorCode($string){
//        preg_match_all('/[\'|"](\d{5}|\d{1,2}X{1}\d{3,5})[\'|"]/im', $string, $matches);
//        foreach($matches[0] as $key=>$value){
//            $matches[0][$key] = trim($value, "\|..'..\"");
//        }
//        return ($matches);
//    }
//
//    private function matchConfErrorCode($string){
//        preg_match_all('/error_\d+[X]*\d+/im', $string, $matches);
//        return implode(',', $matches[0]);
//    }

    /**
     * 根据任务完成量更新用户积分和金币
     * 缓存文件：becfad7778973b59c4ba974fd2025283
     * @author demo 16-3-8
     */
    public function upgradePointsByTask(){
        $cache = \Think\Cache::getInstance('file');
        header('Content-type:text/html;charset=utf8');
        $sql = 'select u.UserID, u.UserName, count(l.MHLID) as times, t.RewardType, t.Reward, t.MHTID from zj_user u left join zj_mission_hall_records r on r.UserID=u.UserID LEFT JOIN zj_mission_hall_log l on l.MHRID=r.MHRID LEFT JOIN zj_mission_hall_tasks t on t.MHTID=r.MHTID where l.`Status`=4 AND (t.RewardType = 1 OR t.RewardType = 3) GROUP BY u.UserID,t.MHTID,t.RewardType Order BY UserID';
        $data = M()->query($sql);
        $page = (int)$_GET['page'];
        if(empty($page)){
            $page = 1;
        }
        $limit = 200;
        $total = ceil(count($data)/$limit);
        $cacheData = $cache->get('upgradePoints');
        if(empty($cacheData)){
            $cacheData = array();
        }
        //查看当前的页数是否从第一页开始并且是连续的
        if(empty($cacheData) && $page != 1){
            exit('请从第一页开始执行！');
        }else if(!empty($cacheData)){
            sort($cacheData);
            $arr = array();
            for($i=1; $i<=$total; $i++){
                $arr[] = $i;
            }
            $diff = array_merge(array_diff($arr, $cacheData));
            if(count($diff) == 0){
                exit('数据已更新');
            }
            $page = $diff[0];
        }
        $sql .= ' LIMIT '.(($page-1)*$limit).','.$limit;
        $data = M()->query($sql);
        foreach($data as $key=>$value){
            $key = ($page-1)*$limit + $key + 1;
            $str = '';
            $product = $value['times']*$value['Reward'];
            $msg = '';
            if($value['RewardType'] == 1){
                $msg = '增加积分：'.$product;
                $str = "Points=Points+{$product}";
            }else if($value['RewardType'] == 3){
                $msg = '增加金币：'.$product;
                $str = "Cz=Cz+{$product}";
            }

            if(!empty($str)){
                $msg = "共完成{$value['times']}次，".$msg.'。更新成功！';
                $sql = "UPDATE zj_user SET {$str} WHERE UserID={$value['UserID']}";
                $result = true;
                $result = M()->execute($sql);
                if($result === false){
                    $msg = '<font color="red">更新失败！</font>';
                }
                // echo $sql.'<br/>';
                echo "{$key}、用户编号为【{$value['UserID']}】，完成编号【{$value['MHTID']}】的任务：{$msg}<br>";
            }
            ob_flush();
            flush();
        }
        $cacheData[] = $page;
        $cache->set('upgradePoints', $cacheData);
        $page++;
        if($page > $total){
            exit('更新完成！');
        }
        $url = '/Manage/Index/upgradePointsByTask/page/'.$page;
        exit("<script>window.open('{$url}','_blank')</script>");
    }

//    /**
//     * 任务大厅完成任务添加pay表中的数据
//     * @author demo 2016-3-18
//     */
//    public function appendDatasOfPayByTask(){
//        header('Content-type:text/html;charset=utf8');
//        $cache = \Think\Cache::getInstance('file');
//        $c = $cache->get('addDataToPay');
//        if(!empty($c)){
//            exit('添加完成！');
//        }
//        $sql = "select COUNT(l.MHLID) AS 'count' FROM zj_mission_hall_log l LEFT JOIN zj_mission_hall_records r ON r.MHRID=l.MHRID LEFT JOIN zj_mission_hall_tasks t ON t.MHTID = r.MHTID WHERE l.Status=4 AND t.RewardType=3";
//        $count = M()->query($sql);
//        $count = (int)$count[0]['count'];
//        if($count === 0){
//            exit('没有需要添加的数据');
//        }
//
//        $page = (int)$_GET['p'];
//        if($page === 0){
//            $page = 1;
//        }
//        $limit = 400;
//        $total = ceil($count/$limit);
//        if($page == $total){
//            $cache->set('addDataToPay', true);
//        }
//        $limitNum  = ($page-1) * $limit;
//        $sql = "select r.UserID, l.AddTime, t.Title, t.Reward FROM zj_mission_hall_log l LEFT JOIN zj_mission_hall_records r ON r.MHRID=l.MHRID LEFT JOIN zj_mission_hall_tasks t ON t.MHTID = r.MHTID WHERE l.Status=4 AND t.RewardType=3 LIMIT {$limitNum},{$limit}";
//        $data = M()->query($sql);
//        foreach($data as $key=>$value){
//            $values = array();
//            $values['PayName'] = "'任务大厅任务'";
//            $values['PayMoney'] = "'{$value['Reward']}'";
//            $values['PayDesc'] = "'{$value['Title']}'";
//            $values['UserID'] = "'{$value['UserID']}'";
//            $values['AddTime'] = "'{$value['AddTime']}'";
//            $values = implode(',', $values);
//            // echo ($limitNum+$key+1).'：';
//            $sql = "INSERT INTO zj_pay(`PayName`,`PayMoney`,`PayDesc`,`UserID`,`AddTime`) VALUE({$values})";
//            // echo $sql.'<br>';
//            M()->execute($sql);
//        }
//        $url = '/Manage/Index/appendDatasOfPayByTask/p/'.(++$page);
//        header("location:{$url}");
//        // exit("<script>window.open('{$url}','_blank')</script>");
//    }

    //去除重复试题等于自身的试题，查找重复试题不存在的试题
    public function removeDuplicateSelf(){
        $testAttrReal=$this->getModel('TestAttrReal');
        //$buffer=$testAttrReal->selectData('TestID','TestID=Duplicate');
        //if($buffer) $testAttrReal->updateData(array('Duplicate'=>0),'TestID=Duplicate');
        $testAttr=$this->getModel('TestAttr');
        //$buffer=$testAttr->selectData('TestID','TestID=Duplicate');
        //if($buffer) $testAttr>updateData(array('Duplicate'=>0),'TestID=Duplicate');

        exit(count($buffer).'aaa');
        //查找重复试题不存在的数据
        header('Content-type:text/html; charset=utf8');
        $buffer=$testAttrReal->selectData('TestID,Duplicate','Duplicate!=0','TestID ASC');
        if($buffer){
            foreach($buffer as $iBuffer){
                $tmp=$testAttrReal->selectData('TestID','TestID='.$iBuffer['Duplicate']);
                if(!$tmp){
                    $tmp=$testAttr->selectData('TestID','TestID='.$iBuffer['Duplicate']);
                    if(!$tmp) echo '试题id：'.$iBuffer['Duplicate'].'不存在<br/>';
                }
            }
        }
    }
}