<?php
/**
 * @author demo   
 * @date 2014年11月3日
 * @updated 2015年1月22日
 */
/**
 * 智能模板组卷类，用于处理智能组卷相关操作
 */
namespace Dir\Controller;
use Common\Controller\DefaultController;
class IndexController extends DefaultController{
    /**
     * 模板组卷首页
     * @author demo
     */
    public function index() {
        //载入考试类别examType 调用缓存 dirExamType
//        $examType = SS('examType');
        $examType = $this->getApiDir('Exam/examType');
        //载入题型
//        $types = SS('typesSubject');
        $types = $this->getApiCommon('Types/typesSubject');
        $types = json_encode($types, JSON_UNESCAPED_UNICODE);
        if(!$types){
            $types=0;
        }
        //载入学科
        $subject=array();
//        $tmp = SS('subject');
        $tmp = $this->getApiCommon('Subject/subject');
        foreach($tmp as $i=>$iTmp){
            $subject[$i]['SubjectName']=$iTmp['SubjectName'];
        }
        $subject=json_encode($subject, JSON_UNESCAPED_UNICODE);
        if(!$subject){
            $subject=0;
        }

        //载入难度
        $diff=array();
        $tmp=$this->getApiCommon('Diff/getDiff');
        foreach($tmp as $i=>$iTmp){
            $diff[$i]=$iTmp[0];
        }
        $diff=json_encode($diff, JSON_UNESCAPED_UNICODE);
        if(!$diff){
            $diff=0;
        }

        //组卷方式数组chooseTest  0按系统模板组卷、1按我的模板组卷、2自定义模板、3编辑系统模板、4编辑我的模板、
        /*$testStyle=array(array('val'=>'0','styleName'=>'按系统模板组卷'),
                         array('val'=>'1','styleName'=>'按我的模板组卷'),
                         array('val'=>'2','styleName'=>'编辑系统模板'),
                         array('val'=>'3','styleName'=>'编辑我的模板'),
                         array('val'=>'4','styleName'=>'自定义模板'));     */

        $testStyle=$this->getApiDir('Template/getTemplate');

        //获取文档属性
        $param=array('style' => 'docType', 'field' => 'TypeID,TypeName,GradeList');
        $testType=$this->getApiDoc('DocType/docTypeCache', $param);
        foreach($testType as $i=>$iTestType){
            if($testType[$i]['TypeID']=="12"){
                unset($testType[$i]);
            }
        }
        //选题方式
        $chooseAttr = $this->getApiDir('Choose/getChoose');

        //获取地区属性
        $param=array('style' => 'area', 'pID' => 0);
        $area=$this->getApiCommon('Area/areaCache', $param);
        $userID=cookie(C('WLN_HOME_USER_AUTH_KEY') . '_UID');
        $schoolName=$this->getModel('User')->getUserSchool($userID)['SchoolName'];

        $pageName='模版组卷';

        //模板赋值
        $this->assign('testStyle',$testStyle);//组卷方式
        $this->assign('testType',$testType); //文档属性
        $this->assign('chooseAttr',$chooseAttr); //创建选题方式数组
        $this->assign('examType',$examType); //载入考试类别
        $this->assign('Types',$types); //载入题型类别
        $this->assign('Diff',$diff); //难度
        $this->assign('Subject',$subject); //学科
        $this->assign('area',$area); //地区
        $this->assign('schoolName',$schoolName); //学校
        $this->assign('pageName',$pageName);
        $this->display();
    }

   /**
    * ajax根据条件获取对应模板；
    * @return array
    * @author demo ,
    */
    public function getTemplateList(){
        //choosetypeid  0 按系统模板组卷  1按我的模板组卷 2 自定义模板
        if($_POST['check']==1){
            $bool=$this->getApiCommon('Power/homeCheckThisPower','Dir/Index/saveSysTemplateList');
            if($bool===true){
                $this->setBack(array(1));
            }else{
                $this->setBack(array(''));
            }
        }
        $chooseTypeID=$_POST['choosetypeid'];
        $examTypeID=$_POST['examtypeid'];
        $subjectID=$_POST['SubjectID'];
        $username=cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $where='1=1';
        $typeWhere="";
        if($chooseTypeID=='0' || $chooseTypeID=='3'){
            $where.= ' and SubjectID='.$subjectID.' and IfDefault=0 and TypeID='.$examTypeID;
        }else if($chooseTypeID=='1' || $chooseTypeID=='4' ||$chooseTypeID==''){
            if(!empty($examTypeID)){
                $typeWhere=' and TypeID='.$examTypeID;
            }
          $where.= ' and SubjectID='.$subjectID.' and IfDefault=1 and UserName="'.$username.'"'.$typeWhere;
        }
        $tempArr=$this->getModel('DirTemplate')->selectData(
             'TempID,TempName,TypeID,SubjectID,UserName,IfDefault,AddTime,UpdateTime',
            $where
        );
        $examType = SS('examType');
        foreach($tempArr as $i=>$iTempArr){//根据考试类别缓存，获取对应的类别名称
            $tempArr[$i]['TypeName']=$examType[$tempArr[$i]['TypeID']]['TypeName'];
            $tempArr[$i]['UpdateTime']=date('Y.n.j',$iTempArr['UpdateTime']);
        }

        $tempRes['content']=$tempArr;
        $this->setBack(array($tempRes));
    }

   /**
    * ajax根据模板ID获取模板内容；
    * @return json
    * @author demo
    */
    public function getTemplateByID(){
        //序列化  反序列化 json
        $arr['status']='1';
        $username=cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $data['TempID']=$_POST['mbid'];
        $where="TempID=".$data['TempID'];
        $result=$this->getModel('DirTemplate')->selectData(
            'Content,IfDefault,UserName',
            $where
        );
        if($username!=$result[0]['UserName'] and $result[0]['IfDefault']!=0){
            $this->setError('30806',1);
        }else if($result){
            $this->setBack(unserialize($result[0]['Content']));
        }
    }

    /**
     * 保存试题模板
     * @return json
     * @author demo,，
     */
    public function saveTemplate(){
        if(empty($_POST)){
            $this->setError('30301',1);
        }
        $content=$_POST['content'];
        $tmpData['UserName'] = cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $dirTemplate=$this->getModel('DirTemplate');
        if(empty($_POST['templateListId'])){
            $doubleName=$dirTemplate->selectData(
                'TempID',
                'TempName="'.$content['tempname'].'" and UserName="'.$tmpData['UserName'].'" and TypeID='.$_POST['typeId']
            );
            if($doubleName){ //判断名称是否存在
                $this->setError('30807',1);
            }
        }
        $tplID=$_POST['templateListId'];
        $tmpData = array();
        $tmpData['TempName'] = $content['tempname'];
        $tmpData['TypeID'] = $_POST['typeId'];
        $tmpData['IfDefault'] = 1;
        $tmpData['Content'] =serialize($content);//模板数据序列化后存入
        $tmpData['AddTime'] = time();
        $tmpData['UpdateTime'] =time();
        $tmpData['SubjectID'] = $_POST['subjectId'];
        $tmpData['OrderID']='99';
        $tmpData['UserName'] = cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $result=false;

        if($tplID==''){
            $result = $dirTemplate->insertData($tmpData);
            if($result){
                $return="模板保存成功！";
                $this->setBack($return);
            }else{
                $return="22001";
                $this->setError($return,1);
            }
        }else{
            $where='TempID='.$tplID;
            $buffer=$dirTemplate->selectData(
                'IfDefault,UserName',
                $where
            );
            if($buffer[0]['IfDefault']==1){
                $tmpData['TempID']=$tplID;
                $tmpData['UpdateTime']=time();
                if($buffer[0]['UserName'] == $tmpData['UserName']){//验证模板与用户是否对应！
                    $result = $dirTemplate->updateData(
                        $tmpData,
                        'TempID='.$tplID
                    );
                }
                if($result){
                    $return='模板替换成功！';
                    $this->setBack($return);
                }else{
                    $return="22002";
                    $this->setError($return,1);
                }
            }
        }
    }

    /**
     * 保存替换系统模板方法,该方法需要判断，保存模板时，是否显示系统模板
     * @author demo
     */
    public function saveSysTemplateList(){
        if(empty($_POST)){
            $this->setError('30301',1);
        }
        $content=$_POST['content'];
        $tplID=$_POST['templateListId'];
        $tmpData = array();
        $tmpData['TempName'] = $content['tempname'];
        $tmpData['TypeID'] = $_POST['typeId'];
        $tmpData['UserName'] = cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $tmpData['IfDefault'] = 1;
        $tmpData['Content'] =serialize($content);//模板数据序列化后存入
        $tmpData['AddTime'] = time();
        $tmpData['UpdateTime'] = time();
        $tmpData['SubjectID'] = $_POST['subjectId'];
        $tmpData['OrderID']='99';
        if($tplID!=''){
            $dirTemplate=$this->getModel('DirTemplate');

            $where='TempID='.$tplID;
            $buffer=$dirTemplate->selectData(
                'IfDefault',
                $where
            );
            if($buffer[0]['IfDefault']!=1){
                $tmpData['TempID']=$tplID;
                $tmpData['IfDefault'] = 0;
                $str=$this->getUserPowerByTag('Dir/Index/saveSysTemplateList');
                if($str !=='all'){
                    $return="30808";
                    $this->setError($return,1);
                }else{
                    $result = $dirTemplate->updateData(
                        $tmpData,
                        'TempID='.$tplID
                    );
                    if($result){
                        $return='系统模板替换成功！';
                        $this->setBack($return);
                    }else{
                        $return="22004";
                        $this->setError($return,1);
                    }
                }
            }
        }
    }

    /**
     * 根据Content进行组卷 返回json
     * Content格式   ：array('SubjectID'=>14,0=>array(0=>array('typeid'='84',0=>array('nums'=>'1','diff'=>'1',...))))
     * json格式：[[[[{"TestID":"试题ID","Test":"试题题文"}]]]]
     * @author demo
     * @date   2014年9月24日
     */
    public function getTestByContent() {
        $test = $this->getModel('TestReal');
        $content    = $_POST['Content'];                             //通过AJAX传递过来的参数
        $searchType = $content['chooseattr'];
        $subjectID  = $content['SubjectID'];                         //学科ID
        $docType    = $content['doctype'];                             //文档类型
        $gradeList  = $content['gradelist'];                         //年级属性
        $areaList  = $content['arealist'];                         //地区属性
        $dataStr='';
        foreach($content as $i => $iContent){                            //循环遍历参数，分卷层

            if($iContent[0]=='null'){
                $partHead[$i]='';
                continue;
            }
            if(!is_numeric($i)) continue;

            $newTypes=array();                                       //定义一个新数组，用于接收一个分卷下的所有试题
            foreach ($iContent as $j => $jContent){                           //循环分卷下的试题题型
                $typeID        = intval($jContent['typeid']);             //获得题型ID
                $ifHidden = $jContent['ifHidden'];
                unset($jContent['ifHidden']);
                unset($jContent['typeid']);                               //屏蔽题型
                $newTest=array();                                    //定义一个新数组，用于接收一个题型下的所有试题题文
                foreach ($jContent as $k => $kContent){                         //循环遍历试题
                    if(!is_numeric($i)) continue;
                    /*配置条件语句开始*/
                    $data=array();
                    if ($subjectID !='') {                        //学科ID的条件
                        $data['SubjectID']=intval($subjectID);
                    }
                    if ($docType !='') {
                        $data['DocTypeID']=$docType;
                    }
                    if ($gradeList) {                                 //年级的条件
                        $data['GradeID']=$gradeList;
                    }
                    if ($areaList) {                                 //地区的条件
                        $data['AreaID']=$areaList;
                    }
                    if ($typeID) {                                 //题型ID的条件
                        $data['TypesID']=$typeID;
                    }
                    if($kContent['rounds'] && $kContent['rounds']!='all'){
                        if($searchType==1) $data['KlID']=$kContent['rounds'];
                        else  $data['ChapterID']=$kContent['rounds'];
                    }
                    if($kContent['testchoose']){                           //是否选做的条件
                        $data['TestStyle']=$kContent['testchoose'];
                    }
                    if(is_numeric($kContent['nums'])){                           //小题数量条件
                        if($kContent['nums']==1) $kContent['nums']=0;
                        $data['TestNum']=$kContent['nums'];
                    }
                    if(!empty($kContent['ifchoose'])){
                        $data['choose']=$kContent['ifchoose'];
                    }
                    if ($kContent['diff']) {                             //难度
                        $data['Diff']=$kContent['diff'];
                    }

                    //对查询结果进行排重
                    if($dataStr!=''){
                        $data['TestID']=substr($dataStr,1);
                        $data['testfilter']=1;
                    }
                    $data['Duplicate']=0; //去重
                    $data['ShowWhere']=array(0,1);//查找符合使用范围的题
                    $kContent['scores']=array_sum(explode(',',$kContent['scores']));//分值

                    /*配置条件语句结束*/
                    //按条件获取试题编号列表
                    $field=array("testid","test","testnum");
                    $order=array('@random');
                    $page=array('page'=>1,'perpage'=>1,'limit'=>1);
                    $list = $test->getTestIndex($field,$data,$order,$page);
                    if($list === false){
                        $this->setError('30504', (IS_AJAX ? 1 : 0));
                    }
                    if($list[1]){                                    //判断是否有试题编号，如果有，进行循环
                        $newTest[$k]=$list[0][0];
                        $newTest[$k]['scores']=$kContent['scores'];
                        $dataStr.=','.$list[0][0]['testid']; //对查询结果进行排重
                    }else{                                        //如果没有找到则输出没有的提示
                        $newTest[$k]['scores']='0';
                        $newTest[$k]['testid']='0';
                        $newTest[$k]['test']='没有符合条件的试题';
                        $newTest[$k]['testnum']='';
                    }
                }
                $newTypes[$j] = $newTest;//组合一个题型下的所有试题
                $newTypes[$j]['ifHidden']=$ifHidden;
            }
            $partHead[$i] = $newTypes;//组合一个分卷下的所有试题
        }

        $tempLate[0] = $partHead;//组合所有分卷内容
        //添加用户操作日志
        $log['UserName']=cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
        $log['AddTime']=time();
        $log['Content']='';
        $log['SubjectID']=$subjectID;
        $log['Param']=serialize($_POST['Temp']);
        $log['TestList']=substr($dataStr,1);
        $tempLate[1]=$this->getModel('LogTplPaper')->insertData($log);
        if(!$tempLate[1]){
            $tempLate[1]=0;
        }
        $this->setBack($tempLate);
    }

    /**
     * 补全用户操作日志
     * @author demo
     * @date 2014年10月17日
     */
    public function updateLog(){
        $id=$_POST['id'];
        if(!$id){
            $this->setError('30309',1);
        }
        $data['Content']=$_POST['Param'];
        $where='PaperID='.$id;
        $logID=$this->getModel('LogTplPaper')->updateData(
            $data,
            $where
        );
        if($logID){
            $this->setBack('');
        }else{
            $this->setError('30309',1);
        }
    }

    /**
     * 根据ajax提交过来的tplID,删除自己的模板
     * @author demo
     */
    public function delDirTplByID(){
        $tplID=$_POST['tplID'];
        if($tplID){
            $userName=cookie(C('WLN_HOME_USER_AUTH_KEY').'_USER');
            $where='UserName="'.$userName.'" and TempID='.$tplID;  //用户只能删除自己的模板
            $result=$this->getModel('DirTemplate')->deleteData($where);
            if($result==false){
                $result=0;
            }else{
                $result=1;
            }
            $this->setBack($result);
        }
    }
}
