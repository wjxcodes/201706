<?php
/**
 * @author demo 
 * @date 2014年8月28日
 * @update 2015年1月26日
 */
/**
 * 试题错误处理通用类
 */
namespace Manage\Controller;
class ProcErrorController extends BaseController {
    /**
     * 页面显示
     * @author demo
     */
    public function index() {
        $subjectArr = SS('subjectParentId'); //获取学科数据集
        $subject = array();
        foreach ($subjectArr as $iSubjectArr) {
            $subject[] = array('id' => $iSubjectArr['SubjectID'], 'name' => $iSubjectArr['SubjectName'], 'readOnly' => true);
            if ($iSubjectArr['sub']) {
                $sub = $iSubjectArr['sub'];
                foreach ($sub as $jSub) {
                    $subject[] = array('id' => $jSub['SubjectID'], 'name' => $jSub['SubjectName'], 'readOnly' => false);
                }
            }
        }
        $cache = SS('pregError');
        $this->assign(array(
            'pageName' => '试题错误处理',
            'subject' => $subject,
            'preg' => $cache,
        ));
        $this->display();
    }
    /**
     * 处理Ajax搜索，返回匹配数据
     * @author demo
     */
    public function searchTest() {
        if (IS_AJAX) {
            $startNo = $_POST['start'];//开始序号
            $size = $_POST['size'];//每次查询多少个
            $subjectID = $_POST['subject'];
            $pregID = $_POST['preg'];
            if (!$startNo || !$size || !$subjectID || !$pregID) {
                $this->setError('30726',1);//请填写完整参数
            }
            $pregCache = SS('pregError');
            $sPreg = stripslashes($pregCache[$pregID]['SearchPreg']);
            $rPreg = stripslashes($pregCache[$pregID]['ReplacePreg']);
            if (!$sPreg || !$rPreg) {
                $this->setError('13005',1);//正则不存在，请刷新页面重试！
            }
            $refusePreg = stripslashes($pregCache[$pregID]['RefusePreg']);
            //更新缓存中的开始序号和数据库中的开始序号
            $pregCache[$pregID]['StartNo'] = $startNo;
            $this->getModel('ProcessErrorPreg')->updateData($pregData = ['StartNo' => $startNo],$where=['PregID'=>$pregID]);
            S('pregError', $pregCache);//这里直接用S，不重新生成了（重新生成需要再查询一遍数据库）

            $testModel = $this->getModel('TestReal');
            $testDb = $testModel->getDataBySubject($subjectID, $startNo, $size);
            if (!$testDb) {
                $this->setError('13006',1,'',$startNo);//'第'.$startNo.'向后'.$size.'没有试题!
            }
            $data = array();
            $callback = function ($matches) use ($sPreg, $rPreg, $refusePreg) {
                return $this->replaceCallback(true, $matches, $sPreg, $rPreg, $refusePreg);
            };
            foreach ($testDb as $i => $iTestDb) {
                //Test
                $testReplace = preg_replace_callback($sPreg, $callback, $iTestDb['Test']);
                if ($testReplace != $iTestDb['Test']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['test'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['Test']));
                    $data[$i]['newTest'] = htmlentities($testReplace);
                }
                //Answer
                $answerReplace = preg_replace_callback($sPreg, $callback, $iTestDb['Answer']);
                if ($answerReplace != $iTestDb['Answer']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['answer'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['Answer']));
                    $data[$i]['newAnswer'] = htmlentities($answerReplace);
                }
                //Analytic
                $analyticReplace = preg_replace_callback($sPreg, $callback, $iTestDb['Analytic']);
                if ($analyticReplace != $iTestDb['Analytic']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['analytic'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['Analytic']));
                    $data[$i]['newAnalytic'] = htmlentities($analyticReplace);
                }
                //Remark
                $remarkReplace = preg_replace_callback($sPreg, $callback, $iTestDb['Remark']);
                if ($remarkReplace != $iTestDb['Remark']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['remark'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['Remark']));
                    $data[$i]['newRemark'] = htmlentities($remarkReplace);
                }
                //DocTest
                $docTestReplace = preg_replace_callback($sPreg, $callback, $iTestDb['DocTest']);
                if ($docTestReplace != $iTestDb['DocTest']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['docTest'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['DocTest']));
                    $data[$i]['newDocTest'] = htmlentities($docTestReplace);
                }
                //DocAnswer
                $docAnswerReplace = preg_replace_callback($sPreg, $callback, $iTestDb['DocAnswer']);
                if ($docAnswerReplace != $iTestDb['DocAnswer']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['docAnswer'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['DocAnswer']));
                    $data[$i]['newDocAnswer'] = htmlentities($docAnswerReplace);
                }
                //DocAnalytic
                $docAnalyticReplace = preg_replace_callback($sPreg, $callback, $iTestDb['DocAnalytic']);
                if ($docAnalyticReplace != $iTestDb['DocAnalytic']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['docAnalytic'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['DocAnalytic']));
                    $data[$i]['newDocAnalytic'] = htmlentities($docAnalyticReplace);
                }
                //DocRemark
                $docRemarkReplace = preg_replace_callback($sPreg, $callback, $iTestDb['DocRemark']);
                if ($docRemarkReplace != $iTestDb['DocRemark']) {
                    $data[$i]['testID'] = $iTestDb['TestID'];
                    $data[$i]['docRemark'] = htmlentities(preg_replace_callback($sPreg, array($this, 'searchCallback'), $iTestDb['DocRemark']));
                    $data[$i]['newDocRemark'] = htmlentities($docRemarkReplace);
                }
            }
            if ($data) {
                //有试题改变
                $this->setBack(array('success',$data));
            } else {
                $sID = $testDb[0]['TestID'];
                $eID = $testDb[$size - 1]['TestID'];
                $this->setBack(array('error','本页未搜索到匹配数据！(试题编号：'.$sID.'-'.$eID.')'));
            }
        }
    }
    /**
     * 原文标红处理
     * @param array $matches 匹配对象数组
     * @return string 替换处理结果
     * @author demo
     */
    private function searchCallback($matches) {
        return '!@#' . $matches[0] . '#@!';
    }
    /**
     * 替换回调函数
     * @param bool $isDis 是否是用来做标红显示的
     * @param array $matches 多个匹配项
     * @param string $sPreg 搜索正则
     * @param string $rPreg 替换正则
     * @param string $refusePreg 排除正则，用于对第一次匹配的项进行排除
     * @return string 替换后的字符串
     * @author demo
     */
    private function replaceCallback($isDis, $matches, $sPreg, $rPreg, $refusePreg) {
        //对匹配的项进行处理
        $start = $isDis === true ? '!@#' : '';
        $end = $isDis === true ? '#@!' : '';
        if ($refusePreg && preg_match($refusePreg, $matches[0])) {
            //如果存在排除正则且符合排除规则，则不需要替换，返回原来值
            $result = $matches[0]; //不变
        } else {
            //不排除，进行替换
            $result = preg_replace($sPreg, $start . $rPreg . $end, $matches[0]);
        }
        return $result;
    }
    /**
     * 题号换算序号
     * @author demo
     */
    public function getNoByTestID() {
        if (IS_AJAX) {
            $subject = $_POST['subject'];
            $testID = $_POST['testID'];
            if (!$subject || !$testID) {
                $this->setError('13007',1);//请选择学科后输入试题编号！
            }
            $testRealModel = $this->getModel('TestReal');
            $no = $testRealModel->getNoByTestID($testID,$subject);
            $this->setBack($no);
        }
    }
    /**
     * 添加正则表达式
     * @author demo
     */
    public function addPreg() {
        if (IS_AJAX) {
            $pregName  = $_POST['name'];
            $searchPreg = $_POST['sPreg'];
            $refusePreg = $_POST['refusePreg'];
            $replacePreg = $_POST['rPreg'];
            $startNo = $_POST['no'];
            if(!$pregName||!$searchPreg||!$replacePreg||!$startNo){//简单验证，之后有验证类的时候完善
                $this->setError('13008',1);//缓存写入失败，请重试！
            }
            $data = array(
                'PregName' => $pregName,
                'SearchPreg' => $searchPreg,
                'RefusePreg' => $refusePreg,
                'ReplacePreg' => $replacePreg,
                'StartNo' => $startNo,
                'SubjectID' => 0,
                'Description' => '',
                'CreateUser' => $this->getCookieUserName(),
                'CreateTime' => time(),
            );
            if ($this->getModel('ProcessErrorPreg')->insertData($data)) {
                $this->generateCache();
                $this->setBack('添加成功！');
            } else {
                $this->setError('13008',1);//缓存写入失败，请重试！
            }
        }
        $this->assign(array(
            'pageName' => '新增错误处理正则',
        ));
        $this->display();
    }
    /**
     * 修改正则表达式
     * @author demo
     */
    public function editPreg() {
           $processModel=$this->getModel('ProcessErrorPreg');
        if (IS_AJAX) {
            //处理提交的修改请求
            $pregID = $_POST['id'];
            $pregName  = $_POST['name'];
            $searchPreg = $_POST['sPreg'];
            $refusePreg = $_POST['refusePreg'];
            $replacePreg = $_POST['rPreg'];
            $startNo = $_POST['no'];
            if(!$pregName||!$searchPreg||!$replacePreg||!$startNo){//简单验证，之后有验证类的时候完善
                $this->setError('13008',1);//缓存写入失败，请重试！
            }
            $data = array(
                'PregName' => $pregName,
                'SearchPreg' => $searchPreg,
                'RefusePreg' => $refusePreg,
                'ReplacePreg' => $replacePreg,
                'StartNo' => $startNo,
                'SubjectID' => 0,
                'Description' => '',
                'UpdateUser' => $this->getCookieUserName(),
                'UpdateTime' => time(),
            );
            $where = ['PregID'=>$pregID];
            if($processModel->updateData($data,$where)){
                $this->generateCache();
                $this->setBack('修改成功！');
            }else{
                $this->setError('13010',1);//修改失败！error：缓存写入失败
            }
        }
        //渲染页面
        $where = ['PregID'=>$_GET['id']];
        $dbData = $processModel->findData('*',$where);
        if (!$dbData) {
            redirect(U('ProcError/addPreg'));
        }
        $preg = array(
            'PregID' => $dbData['PregID'],
            'PregName' => stripslashes($dbData['PregName']),
            'SearchPreg' => stripslashes($dbData['SearchPreg']),
            'ReplacePreg' => stripslashes($dbData['ReplacePreg']),
            'RefusePreg' => stripslashes($dbData['RefusePreg']),
            'StartNo' => $dbData['StartNo'],
        );
        $this->assign(array(
            'pageName' => '修改错误处理正则',
            'preg' => $preg,
        ));
        $this->display();
    }
    /**
     * 删除正则表达式
     * @author demo
     */
    public function deletePreg() {
        if (IS_AJAX) {
            $where = ['PregID'=>$_POST['id']];
            if($this->getModel('ProcessErrorPreg')->deleteData($where)){
                $this->generateCache();
                $this->setBack('删除成功！');
            }else{
                $this->setError('13012 ',1);//删除失败请重试！error:重新写入缓存失败
            }
        }
    }

    /**
     * cache转换到数据库
     * 执行一次即可
     * @author demo
     */
//    private function cacheToDb(){
//        $cache = S('pregError')['preg'];
//        if($cache){
//            foreach($cache as $iCache){
//                echo '<meta charset="utf-8">';
//                print_r($iCache);
//                $data = array(
//                    'pregName' => $iCache['name'],
//                    'searchPreg' => $iCache['sPreg'],
//                    'refusePreg' => $iCache['refusePreg'],
//                    'replacePreg' => $iCache['rPreg'],
//                    'startNo' => $iCache['no'],
//                    'subjectID' => 0,
//                    'description' => '',
//                    'createUser' => cookie(C('WLN_WLN_USER_AUTH_KEY')),
//                    'createTime' => time(),
//                );
//                if ($this->getModel('ProcessErrorPreg')->insertData($data)) {
//                    echo '[insertDB ok]';
//                } else {
//                    echo '[------------------error------------------]';
//                }
//                echo '<br>';
//            }
//            $this->generateCache();
//        }else{
//            exit('no need cache');
//        }
//    }
    /**
     * 生成后台试题纠错正则列表缓存数组
     * 数组键为pregID值为数组
     * @author demo
     */
    private function generateCache() {
        $processErrorPregModel = $this->getModel('ProcessErrorPreg');
        $processErrorPregModel->setCache();
    }
    /**
     * 正则表达式列表的显示页面
     * @author demo
     */
    public function pregList() {
        $list = SS('pregError');
        foreach ($list as $i => $iList) {
            $list[$i]['id'] = $iList['id'];
            $list[$i]['name'] = htmlentities(stripslashes($iList['name']));
            $list[$i]['sPreg'] = htmlentities(stripslashes($iList['SearchPreg']));
            $list[$i]['refusePreg'] = htmlentities(stripslashes($iList['RefusePreg']));
            $list[$i]['rPreg'] = htmlentities(stripslashes($iList['ReplacePreg']));
        }
        $this->assign(array(
            'pageName' => '错误处理正则列表',
            'list' => $list,
        ));
        $this->display();
    }
    /**
     * 处理指定TestID的试题内容
     * @author demo
     */
    public function replaceTest() {
        if (IS_AJAX) {
            $testIDs = $_POST['testID'];
            $pregID = $_POST['preg'];
            if (!$testIDs || !$pregID) {
                $this->setError('13013 ',1);//请选择正则搜索后选择试题替换！
            }
            $pregCache = SS('pregError');
            if (!$pregCache[$pregID]) {
                $this->setError('13014 ',1);//正则不存在！
            }
            $sPreg = stripslashes($pregCache[$pregID]['SearchPreg']);
            $rPreg = stripslashes($pregCache[$pregID]['ReplacePreg']);
            $refusePreg = stripslashes($pregCache[$pregID]['RefusePreg']);
            //查询需要替换的试题
            $testRealModel = $this->getModel('TestReal');
            $testData = $testRealModel->getDataByTestIDs($testIDs);
            //批量更新试题
            $update = array();//更新数组
            $errorID = array();//未被更新的试题的TestID数组
            $successID = array();//已经被更新的TestID的数组
            $callback = function ($matches) use ($sPreg, $rPreg, $refusePreg) {
                return $this->replaceCallback(false, $matches, $sPreg, $rPreg, $refusePreg);
            };
            foreach ($testData as $i => $iTestData) {
                $content = $iTestData['Test'] . $iTestData['Answer'] . $iTestData['Analytic'] . $iTestData['Remark'];
                $docContent = $iTestData['DocTest'] . $iTestData['DocAnswer'] . $iTestData['DocAnalytic'] . $iTestData['DocRemark'];
                if (preg_match( $sPreg , $content) || preg_match( $sPreg , $docContent)) {
                    $update[$i]['TestID'] = $iTestData['TestID'];
                    $update[$i]['Test'] = preg_replace_callback( $sPreg , $callback, $iTestData['Test']);
                    $update[$i]['Answer'] = preg_replace_callback( $sPreg , $callback, $iTestData['Answer']);
                    $update[$i]['Analytic'] = preg_replace_callback( $sPreg , $callback, $iTestData['Analytic']);
                    $update[$i]['Remark'] = preg_replace_callback($sPreg,$callback,$iTestData['Remark']);
                    $update[$i]['DocTest'] = preg_replace_callback( $sPreg , $callback, $iTestData['DocTest']);
                    $update[$i]['DocAnswer'] = preg_replace_callback( $sPreg , $callback, $iTestData['DocAnswer']);
                    $update[$i]['DocAnalytic'] = preg_replace_callback( $sPreg , $callback, $iTestData['DocAnalytic']);
                    $update[$i]['DocRemark'] = preg_replace_callback($sPreg,$callback,$iTestData['DocRemark']);
                    $successID[] = $iTestData['TestID'];
                }else{
                    //没有匹配上的试题
                    $errorID[] = $iTestData['TestID'];
                }
            }
            $testRealModel->updateErrorData($update);
            $this->setBack(array(
                'successID'=>$successID,
                'errorID'=>$errorID,
            ));
        }
    }
    /**
     * 获取单题html和doc数据
     * @author demo
     */
    public function getSingleTest(){
        $testID=$_POST['testID'];
        if(empty($testID)){
            $this->setError('30301',1);
        }
        if(!is_numeric($testID)){
            $this->setError('30502',1);
        }
        $testReal=$this->getModel('TestReal');
        $buffer=$testReal->getDataByTestIDs($testID);
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $buffer[$i]['Test'] = $this->changeR2BR(htmlentities($iBuffer['Test']));
                $buffer[$i]['Answer'] = $this->changeR2BR(htmlentities($iBuffer['Answer']));
                $buffer[$i]['Analytic'] = $this->changeR2BR(htmlentities($iBuffer['Analytic']));
                $buffer[$i]['Remark'] = $this->changeR2BR(htmlentities($iBuffer['Remark']));
                $buffer[$i]['DocTest'] = $this->changeR2BR(htmlentities($iBuffer['DocTest']));
                $buffer[$i]['DocAnswer'] = $this->changeR2BR(htmlentities($iBuffer['DocAnswer']));
                $buffer[$i]['DocAnalytic'] = $this->changeR2BR(htmlentities($iBuffer['DocAnalytic']));
                $buffer[$i]['DocRemark'] = $this->changeR2BR(htmlentities($iBuffer['DocRemark']));
            }
            $this->setBack($buffer[0]);
        }else{
            $this->setBack('');
        }
    }
    /**
     * 替换单题html和doc数据
     * @author demo
     */
    public function replaceSingleTest(){
        $testID=$_POST['testID'];
        $oldStr=stripslashes($_POST['oldStr']);
        $replaceStr=$_POST['replaceStr'];
        $flag=$_POST['flag'];
        if(empty($testID)){
            $this->setError('13001',1);
        }
        if(!is_numeric($testID)){
            $this->setError('30502');
        }
        if(empty($oldStr)){
            $this->setError('13002',1); //请填写替换规则
        }
        //获取试题数据
        $testReal=$this->getModel('TestReal');
        $buffer=$testReal->getDataByTestIDs($testID);
        $newOld='!@#'.$oldStr.'#@!';
        if($buffer){
            if($flag==0 && $replaceStr!=''){
                $replaceStr='!@#'.$replaceStr.'#@!';
            }
            foreach($buffer as $i=>$iBuffer){
                if($flag==1){
                    //声明数据长度差值
                    $htmlFlag=0; //html替换前后数据差值
                    $docFlag=0; //doc替换前后数据差值
                    $preLength=0; //替换前数据长度
                    $nextLength=0; //替换后数据长度
                    //声明替换规则
                    $param=array();
                    $param['oldStr']=$oldStr;
                    $param['replaceStr']=$replaceStr;
                    $param['test']=$iBuffer['Test'];
                    $param['hang']="\r\n";
                    $param['flag']=1;
                    //题文替换
                    $preLength=strlen($iBuffer['Test']); //替换前数据长度
                    $buffer[$i]['Test'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['Test']); //替换后数据长度
                    $htmlFlag=$preLength-$nextLength; //数据替换前后之差

                    $param['test']=$iBuffer['DocTest'];
                    $preLength=strlen($iBuffer['DocTest']);
                    $buffer[$i]['DocTest'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['DocTest']);
                    $docFlag=$preLength-$nextLength;
                    if($htmlFlag!=$docFlag){
                        $this->setError('13015',1);
                    }
                    //答案替换
                    $param['test']=$iBuffer['Answer'];
                    $preLength=strlen($iBuffer['Answer']); //替换前数据长度
                    $buffer[$i]['Answer'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['Answer']); //替换后数据长度
                    $htmlFlag=$preLength-$nextLength; //数据替换前后之差
                    
                    $param['test']=$iBuffer['DocAnswer'];
                    $preLength=strlen($iBuffer['DocAnswer']);
                    $buffer[$i]['DocAnswer'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['DocAnswer']);
                    $docFlag=$preLength-$nextLength;
                    if($htmlFlag!=$docFlag){
                        $this->setError('13015',1);
                    }
                    //解析替换
                    $param['test']=$iBuffer['Analytic'];
                    $preLength=strlen($iBuffer['Analytic']);
                    $buffer[$i]['Analytic'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['Analytic']);
                    $htmlFlag=$preLength-$nextLength; //数据替换前后之差

                    $param['test']=$iBuffer['DocAnalytic'];
                    $preLength=strlen($iBuffer['DocAnalytic']);
                    $buffer[$i]['DocAnalytic'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['DocAnalytic']);
                    $docFlag=$preLength-$nextLength;
                    if($htmlFlag!=$docFlag){
                        $this->setError('13015',1);
                    }
                    //备注替换
                    $param['test']=$iBuffer['Remark'];
                    $preLength=strlen($iBuffer['Remark']);
                    $buffer[$i]['Remark'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['Remark']);
                    $htmlFlag=$preLength-$nextLength; //数据替换前后之差

                    $param['test']=$iBuffer['DocRemark'];
                    $preLength=strlen($iBuffer['DocRemark']);
                    $buffer[$i]['DocRemark'] = $this->replaceR2BR($param);
                    $nextLength=strlen($buffer[$i]['DocRemark']);
                    $docFlag=$preLength-$nextLength;
                    if($htmlFlag!=$docFlag){
                        $this->setError('13015',1);
                    }
                }else if($flag==0){
                    $param=array();
                    $param['oldStr']=$oldStr;
                    $param['replaceStr']=$replaceStr;
                    $param['test']=$iBuffer['Test'];
                    $param['hang']="<br>";
                    $param['flag']=0;
                    $buffer[$i]['NewTest'] = $this->replaceR2BR($param);

                    $param['test']=$iBuffer['Answer'];
                    $buffer[$i]['NewAnswer'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['Analytic'];
                    $buffer[$i]['NewAnalytic'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['Remark'];
                    $buffer[$i]['NewRemark'] = $this->replaceR2BR($param);

                    $param['test']=$iBuffer['DocTest'];
                    $buffer[$i]['NewDocTest'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocAnswer'];
                    $buffer[$i]['NewDocAnswer'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocAnalytic'];
                    $buffer[$i]['NewDocAnalytic'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocRemark'];
                    $buffer[$i]['NewDocRemark'] = $this->replaceR2BR($param);

                    $param['replaceStr']=$newOld;
                    $param['test']=$iBuffer['Test'];
                    $buffer[$i]['Test'] = $this->replaceR2BR($param);
                    //$buffer[$i]['Test'] = $this->changeR2BR(htmlentities(str_replace($oldStr,$newOld,$iBuffer['Test'])));

                    $param['test']=$iBuffer['Answer'];
                    $buffer[$i]['Answer'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['Analytic'];
                    $buffer[$i]['Analytic'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['Remark'];
                    $buffer[$i]['Remark'] = $this->replaceR2BR($param);

                    $param['test']=$iBuffer['DocTest'];
                    $buffer[$i]['DocTest'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocAnswer'];
                    $buffer[$i]['DocAnswer'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocAnalytic'];
                    $buffer[$i]['DocAnalytic'] = $this->replaceR2BR($param);
                    $param['test']=$iBuffer['DocRemark'];
                    $buffer[$i]['DocRemark'] = $this->replaceR2BR($param);
                }
            }
        }else{ //没有找到试题
            $this->setBack('没有找到试题!');
        }
        if($flag==1){ //替换数据
            $testdoc=$this->getModel('TestDoc');
            $result=true;
            foreach($buffer as $i=>$iBuffer){
                $data=array();
                $data['TestID']=$iBuffer['TestID'];
                $data['Test']=$iBuffer['Test'];
                $data['Answer']=$iBuffer['Answer'];
                $data['Analytic']=$iBuffer['Analytic'];
                $data['Remark']=$iBuffer['Remark'];
                $result=$this->getModel('TestReal')->updateData($data,'TestID='.$iBuffer['TestID']);
                $data=array();
                $data['TestID']=$iBuffer['TestID'];
                $data['DocTest']=$iBuffer['DocTest'];
                $data['DocAnswer']=$iBuffer['DocAnswer'];
                $data['DocAnalytic']=$iBuffer['DocAnalytic'];
                $data['DocRemark']=$iBuffer['DocRemark'];
                if($result){
                    $result=$testdoc->updateData($data,'TestID='.$iBuffer['TestID']);
                    if(!$result){
                        $this->setError(13003,1);
                    }
                }else{
                    $this->setError(13003,1);
                }
            }
            $this->setBack($testID);
        }elseif($flag==0){ //预览替换数据
            $this->setBack($buffer[0]);
        }
    }
    /**
     * 转义文本换行
     * @author demo
     */
    protected function changeR2BR($str){
        $str=str_replace("\n","<br>",$str);
        return str_replace("\r","",$str);
    }
    /**
     * 转义文本换行
     * @author demo
     */
    protected function changeR2Str($str){
        $str=str_replace("\n","$#$",$str);
        return str_replace("\r","",$str);
    }
    /**
     * 替换文本换行
     * @param array $param 数据参数（oldStr 需要替换的数据 replaceStr替换后的数据 Test文本数据 hang换行的标志）
     * @return string
     * @author demo
     */
    protected function replaceR2BR($param){
        $oldStr=$this->changeR2Str($param['oldStr']);
        $replaceStr=$this->changeR2Str($param['replaceStr']);
        $test=$this->changeR2Str($param['test']);
        if($param['flag']==0) $test = htmlentities(str_replace($oldStr,$replaceStr,$test));
        else $test = str_replace($oldStr,$replaceStr,$test);
        return str_replace("$#$",$param['hang'],$test);
    }
}