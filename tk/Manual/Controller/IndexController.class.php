<?php
/**
 * @author demo
 * @date 2015年11月2日
 */
/**
 * 手动组卷控制器类，用于处理手动组卷操作
 */
namespace Manual\Controller;
use Common\Controller\DefaultController;
class IndexController extends DefaultController {
    /**
     * 试题知识点浏览
     */
    public function zsd() {
        $pageName = "按知识点选题";
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 试题关键字浏览
     */
    public function gjz() {
        $pageName = "按关键字选题";
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 关键字添加
     */
    public function addKeyWord(){
        if(empty($_POST['keywords'])  && $_POST['keywords']!=0){
            $result='关键字格式错误！';
            $this->setBack($result);
        }else{
            $username = $this->getCookieUserName();
            $ok=$this->getModel('LogSearch')->addKeyWord($_POST['keywords'],$_POST['subjectID'],$username);
            if(!$ok){
                $result='false';
                $this->setBack($result);
            }else{
                $this->setBack('true');
            }
        }
    }

    /**
     * 试题试卷浏览
     */
    public function sj() {
        $pageName = "按试卷出题";
        $docID=$_GET['docID'];
        $subjectID=$_GET['subjectID'];
        $this->assign('showDocID', $docID);
        $this->assign('showSubjectID', $subjectID);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 试题章节浏览
     */
    public function zj() {
        $pageName = "按章节选题";
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * ajax获取知识点的初始载入属性
     */
    public function getZsdInit(){
        $output = array ();
        $subjectID = $_POST['id'];
        //题型
        $param['style']='types';
        $param['subjectID'] = $subjectID;
        $param['return'] = 2;
        $output[1] = $this->getData($param);
        //难度
        $param2['style']='diff';
        $param2['return'] = 2;
        $output[2] = $this->getData($param2);
        //知识点数据
        $param=array();
        $param['style']='knowledge';
        $param['subjectID']=$subjectID;
        $param['haveLayer']=3;
        $param['return']=2;
        $output[3] = $this->getData($param);
        //试卷属性
        $output[4] = $this->getModel('DocType')->getDocAttr();
        //文档来源
        $output[5] = SS('docSource');
        //$output[4] = $this->getArea(); //试卷省份
        $this->setBack($output);
    }

    /**
     * 获取试卷的初始数据
     */
    public function getDocInit(){
        $output = array ();
        $subjectID = $_GET['id'];
        //年级属性
        $classGrade=SS('gradeListSubject');
        $subject=SS('subject');

        $output[1] = $this->getModel('DocType')->getDocAttr(0,1); //试卷属性 隐藏不需要显示的数据
        $output[2] = $this->getArea(); //试卷省份
        $output[3] = $classGrade[$subject[$subjectID]['PID']]['sub']; //年级
        $this->setBack($output);
    }
    /**
     * ajax获取试卷和对应试题
     */
    public function getDocTest(){
        $docID = $_REQUEST['did']; //试卷
        $doc=$this->getModel('Doc');
        $where=array('DocID'=>$docID);
        $field=array('docid','docname','typename','docyear','areaname','loadtime','introfirsttime','introtime');
        $page=array('page'=>1,'perpage'=>1,'limit'=>1);
        $output[0]=$doc->getDocIndex($field,$where,'',$page);

        $where=array('DocID'=>$docID);
        $field=array('testid','test','diff','docname','typesname','typesid','testnum','firstloadtime');
        $page=array('page'=>1,'perpage'=>100,'limit'=>100);
        $order=array(0=>'testid ASC');
        $output[1]=$this->getTest($field,$where,$order,$page);
        if($output[1]){
            $this->setBack($output);
        }else{
            $this->setBack('抱歉！试卷不存在。');
        }
    }

    /**
     * @param $field
     * @param $where
     * @param $order
     * @param $page
     * @param int $reload
     * @return mixed
     */
    protected function getTest($field,$where,$order,$page,$reload=0){
        $TestReal=$this->getModel('TestReal'); //试题
        $tmpStr=$TestReal->getTestIndex($field,$where,$order,$page);
        if($tmpStr === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }
        if($reload) $tmpStr[0]=R('Common/TestLayer/reloadTestArr',array($tmpStr[0]));
        return $tmpStr;
    }
    
    /**
     * ajax获取省份
     * @return array
     */
    protected function getArea() {
        $output = array ();
        $buffer = SS('areaChildList');
        if($buffer[0]){
            foreach($buffer[0] as $i=>$iBuffer){
                $output[$i]['AreaID']=$iBuffer['AreaID'];
                $output[$i]['AreaName']=$iBuffer['AreaName'];
            }
        }
        return $output;
    }

    /**
     * 试题word下载
     * @author demo
     */
    public function singleDown() {
        $testID = $_GET['id']; //获取数据标识
        //$w = $_GET['w']; //获取数据标识
        //判断数据标识
        if (empty ($testID)) {
            $this->setError('30301',0);
        }

        //记录历史下载 验证下载次数每月10题
        $userID=$this->getCookieUserID();
        $logTestDown=$this->getModel('LogTestDown');
        $logTestDown->insertData(array(
            'TestID'=>$testID,
            'UserID'=>$userID,
            'AddTime'=>time(),
            'ThisIP'=>ip2long(get_client_ip(0,true)),
        ));

        R('Common/TestLayer/singleDown',array($testID,1));
    }
}