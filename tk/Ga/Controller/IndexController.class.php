<?php
/**
 * @author demo
 * @date 15-10-14 下午12:04
 * @update 15-10-14 下午12:04
 */
/**
 * 组卷端智能组卷模块
 */
namespace  Ga\Controller;
use Common\Controller\DefaultController;
class IndexController extends DefaultController{
    /**
     * 智能组卷首页
     * @return string
     * @author demo
     */
    public function index(){
        $pageName='智能组卷';

        //模板赋值
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * ajax根据科目获取题型 获取文档属性列表
     * @author demo
     */
    public function getTypes() {
        $id=$_GET['id'];    //获取科目id

        //题型数据集
        $output=array();
        $typeArray = $this->getApiCommon('Types/typesSubject');
        $output[0]=$typeArray[$id];
        unset($typeArray);

        //文档属性数据集
        $docParam['style']='docType';
        $docParam['ifDel']='1';
        $docParam['return']='2';
        $docTypeList=$this->getApiDoc('DocType/docTypeCache',$docParam);
        $output[1]=$docTypeList;
        //年级属性
        $param['style']='grade';
        $param['subjectID'] = $id;
        $param['return'] = 2;
        $gradeArray = $this->getApiCommon('Grade/gradeCache',$param);
        $output[2]=$gradeArray;

        $userName=$this->getCookieUserName();
        $output[3]=$this->getApiUser('User/checkUserGrade',$userName,$gradeArray);

        $userIp = get_client_ip(0,true);
        $area = $this->getIPLocBySina($userIp);
        $areaTmp=$this->getApiCommon('Area/areaChildList');
        $types = $this->getApiCommon('Types/types');
        $output[4]=D('TypesDefault')->getTypeDefault($id,$area,$areaTmp,$types);

        $param['style']='typesIntel';
        $param['subjectID']=$id;
        $param['return']=2; //设置返回数组
        $intelName=$this->getApiCommon('Types/typesIntelCache',$param);

        $output[5]=$intelName;
        $this->setBack($output);
    }

    /**
     * 根据新浪IP查询接口获取IP所在地
     * @param $queryIP string 客户端ip地址
     * @return string ip所在地
     * @author demo
     */
    public function getIPLocBySina($queryIP){
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_ENCODING ,'utf8');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        $location = curl_exec($ch);
        $location = json_decode($location);
        curl_close($ch);
        $loc = "";
        if($location===FALSE) return "";
        if (empty($location->desc)) {
            $loc = $location->province.$location->city.$location->district.$location->isp;
        }else{
            $loc = $location->desc;
        }
        return $loc;
    }

    /**
     * ajax根据属性组卷
     */
    public function ga() {
        $subject=$_POST['subject'];     //学科
        $docType=$_POST['doctype'];     //文档类型
        $typeList=$_POST['typelist'];     //试题类型数量
        $numList=$_POST['numlist'];        //试题分值
        $typeStyle=$_POST['typestyle'];    //试题类型id
        $typeScore=$_POST['typesscore'];    //试题计分方式
        $selectType=$_POST['selecttype'];    //试题选题方式
        $gradeID=$_POST['gradeid'];        //年级
        $klList=$_POST['kllist'];        //知识点
        $diffFloat=$_POST['diff'];        //难度值
        $klPer=$_POST['kl'];        //知识点覆盖率
        $klFromChapter=$_POST['klfromchapter'];//知识点来自于章节版本 值为true或者空
        $chooseType=$_POST['choosetype'];    //选做情况
        $chooseNum=$_POST['choosenum'];        //选做数量
        $intelNum=$_POST['intelNum'];        //试题小题数量

        //提取试题    按照知识点和试题类型抽取符合要求的题  随机抽取  对分值和难度值进行评估    进行智能组卷
        $param=array();
        $param['SubjectID']=$subject; //设置学科
        $param['DocType']=$docType; //设置文档类型
        $param['KlType']=$klFromChapter; //设置是否是知识点或者章节
        $param['KlCover']=$klList; //设置知识点或章节 id
        $param['Diff']=$diffFloat; //设置难度值
        $param['KlPer']=$klPer; //设置知识点覆盖率
        $param['Grade']=$gradeID; //设置年级
        $param['Choose']=array($chooseType,$chooseNum); //设置选做情况
        $param['Types']=array($typeStyle,$typeList,$numList,$typeScore,$selectType,$intelNum); //设置试题类型

        $output=array();
        $ga=D('Ga');
        $buffer=$ga->main(20,$param);

        if(!$buffer) $this->setBack('抱歉！没有找到试题。');

        //获取试题属性 以题型间隔  包括计算分值、选做序号、选做数量
        $testArr = $this->getTestByID($buffer[0]); //分卷 题型 试题数据集


        $numList=explode(',',$numList); //试题分值
        $typeScore=explode(',',$typeScore); //试题计分方式
        $chooseNum=explode(',',$chooseNum); //选做数量
        $chooseType=explode(',',$chooseType); //选做情况

        $numAdd=0; //选做序号
        $typesTotal=array(); //分题型统计

        if($buffer[1]){
            foreach($buffer[1] as $i=>$iBuffer){
                $typesTotal[$i]['testNum']=0; //总题数
                $typesTotal[$i]['score']=0; //总分值
                $typesTotal[$i]['preTest']=$numList[$i]; //每题分值
                if($chooseType[$i]==1) $typesTotal[$i]['chooseNum']=$chooseNum[$i]; //选做数量
                else  $typesTotal[$i]['chooseNum']=0; //选做数量

                if($chooseType[$i]==1 && $iBuffer[0]!=0) $numAdd++;

                foreach($iBuffer as $j=>$jBuffer){

                    if($jBuffer==0){
                        break; //基于为0的数据都在最后面
                    }

                    //当前试题分值 选做序号 选做数量
                    $score=0; //试题分值
                    $testNum=$jBuffer['testnum'];
                    if($testNum==0) $testNum=1;

                    //赋值试题属性
                    $buffer[1][$i][$j]=$testArr[$jBuffer['testid']];

                    //试题分值
                    if ($typeScore[$i] == 1) {
                        $score = $numList[$i]*$testNum;
                        $tmpArr=array();
                        for($k=0;$k<$testNum;$k++){
                            $tmpArr[]=$numList[$i];
                        }
                        $buffer[1][$i][$j]['score']=implode(',',$tmpArr);
                    }
                    else if ($typeScore[$i] == 2) {
                        $score = $numList[$i];
                        $buffer[1][$i][$j]['score']=$score;

                        //小题数大于1 分值需要分割到小题上
                        if($testNum>1){
                            $buffer[1][$i][$j]['score']=$this->getApiTest('Test/gaCutScore',$score,$testNum);
                            $typesTotal[$i]['preTest']=0;
                        }
                    }

                    $typesTotal[$i]['testNum']+=$testNum;

                    //选做序号 选做数量
                    if($chooseType[$i]==1) {
                        $buffer[1][$i][$j]['chooseType']=$numAdd;
                        $buffer[1][$i][$j]['chooseNum']=$chooseNum[$i];

                        //选做题分数特殊情况 选出的数量没有达到要求 分值如何计算
                        $typesTotal[$i]['score']=$score*$chooseNum[$i];
                    }else{
                        $typesTotal[$i]['score']+=$score;
                        $buffer[1][$i][$j]['chooseType']=0;
                        $buffer[1][$i][$j]['chooseNum']=0;
                    }
                }
            }
        }

        $output[0] = $buffer[1]; //试题属性
        $output[1] = $buffer[2]; //当前匹配度
        $output[2] = round($buffer[3], 3); //试卷难度
        $output[3] = $buffer[4]; //试卷总分值
        $output[4] = $typesTotal; //试卷分题型统计信息


        $param = array();
        //写入智能组卷日志
        $param['kl']         = $klList;
        $param['doctype']    = $docType;
        $param['gradeid']    = $gradeID;
        $param['testnum']    = $typeList;
        $param['typesid']    = $typeStyle;
        $param['score']      = $numList;
        $param['choosetype'] = $chooseType;
        $param['choosenum']  = $chooseNum;
        $param['diff']       = $diffFloat;
        $param['klcover']    = $klPer;

        /*****************
         *  该步骤将进行智能组卷日志记录，如下面修改，请根据对应内容修改下面内容
         *****************/
        $testIDList='';
        if($buffer[0]){
            $testIDList=implode(',',$buffer[0]);
        }
        $data['Param']       = serialize($param);
        $data['AddTime']     = time();
        $data['UserName']    = $this->getCookieUserName();
        $data['TestList']    = $testIDList; //$buffer[0]为数组中选中的试题ID数组
        $data['SubjectID']   = $subject;
        $logIntellPaper=D('LogIntellPaper');
        $logID=$logIntellPaper->insertData($data);

        $output[5] = $logID; //智能组卷记录id
        $this->setBack($output);
    }

    /**
     * ajax用户点击预览后，把试卷cookie内容存入数据库
     * return bool
     * @author demo
     */
    public function editContent(){
        $data['Content']=$_POST['Content'];
        $data['PaperID']=$_POST['ID'];
        $logIntellPaper=D('LogIntellPaper');
        $result=$logIntellPaper->updateData($data,
            'PaperID='.$_POST['ID']);
        if($result){
            $this->setBack('OK');
        }else{
            $this->setBack('false');
        }
    }

    /**
     * ajax获取试题
     * @param string $id 以逗号间隔的试题id
     * @return array 返回以试题id为键的数据
     */
    public function getTestByID($id){
        if(empty($id)) return ;
        $width = 500;

        $where=array('TestID'=>implode(',',$id));
        $field=array('testid','typesid','typesname','testnum','test','diff','docname','firstloadtime');
        $page=array('page'=>1,'perpage'=>100);
        $tmpStr=$this->getApiTest('Test/getTestIndex',$field,$where,'',$page);

        if($tmpStr === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        $tmpStr=$this->getApiTest('Test/reloadTestArr',$tmpStr[0]);
        return $tmpStr;
    }
}