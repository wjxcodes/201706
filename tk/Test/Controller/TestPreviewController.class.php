<?php
/**
 * 官网试题预览
 * @author demo 16-01-23
 */
namespace Test\Controller;
class TestPreviewController extends BaseController{
    protected $relModuleName = 'Index';//对应的模块名称
    private $isAjax = 0;
    public function __construct(){
        parent::__construct();
        $this->isAjax = (IS_AJAX ? 1 : 0);
        // $isLogin = $this->checkLogin('Index', 0);
        // if(!is_array($isLogin)){
        //     $this->setError('1X2017', $this->isAjax);
        // }else if(isset($isLogin[1]) && 'student' == $isLogin[1]){
        //     $this->setError('1X2016', $this->isAjax);
        // }
    }
    
    /**
     * 试卷预览，试题详情
     * @author demo 16-1-26
     */
    public function testDetail(){
        $id = $_GET['id'];
        $relevantTest = array();
        $testAttrReal = $this->getModel('TestAttrReal');
        $docid = (int)$testAttrReal->findData('DocID', "TestID={$id}")['DocID'];
        if(!empty($docid)){
            $doc = $this->getModel('Doc')->getDocIndex(
                array('typename','subjectid'),
                array('DocID'=>$docid),
                array(),
                array('perpage'=> 1)
            );
            $doc = $doc[0][0];
            $this->assign('typename', $doc['typename']);
            $this->assign('docSubejctId' , $doc['subjectid']);
            $this->assign('docid' , $docid);
            $relevantTest = $testAttrReal->selectData('TestID', 'DocID='.$docid, 'NumbID');
            $last = 0;
            while(count($relevantTest) > 0){
                $current = array_shift($relevantTest);
                if($current['TestID'] != $id){
                    $last = $current['TestID'];
                }else{
                    $current = array_shift($relevantTest);
                    $relevantTest = array();
                    if($last != 0){
                        $relevantTest[] = $last;
                    }
                    $relevantTest[] = (int)$id;
                    if(!is_null($current)){
                        $relevantTest[] = (int)$current['TestID'];
                    }
                    break;
                }
            }
        }
        $buffer = SS('typesSubject');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }
        $types = json_encode($buffer, JSON_UNESCAPED_UNICODE);
        $this->assign('types', $types);
        $this->assign('relevantTest' , $relevantTest);
        
        $result = $this->getSimilarlyTest($id);
        $this->assign('similarity' , $result['similarity']);
        
        $result['test']['title']=formatString('getHtmlDescription',$result['test']['test'],30);
        $result['test']['description']=formatString('getHtmlDescription',$result['test']['test'],100);
        
        $this->assign('result', $result['test']);
        unset($result);
        $comments = $this->getModel('Message')->getMessagesById($id, 1, 5);
        $pagtion = $this->fillPagtion(array('id'=>$id), $comments['count'], 1, $comments['prepage'], '/Test/TestPreview/commentList', 5);
        $this->assign('pagtion', $pagtion['pages']);
        $this->assign('comments', $comments);
        $this->display();
    }
    /**
     * 获取评论列表
     * @author demo 16-1-27
     */
    public function commentList(){
        $page = (int)$_GET['p'];
        $id = (int)$_GET['id'];
        if(empty($page)){
            $page = 1;
        }
        $comments = $this->getModel('Message')->getMessagesById($id, $page, 5);
        foreach($comments['data'] as $key=>$value){
            $comments['data'][$key]['LoadDate'] = date('Y/m/d H:i:s', $value['LoadDate']);
        }
        $pagtion = $this->fillPagtion(array('id'=>$id), $comments['count'], $page, $comments['prepage'], '/Test/TestPreview/commentList', 5);
        $this->setBack(array('pagtion'=>$pagtion['pages'], 'comments' => $comments));
    }

    /**
     * 获取下一道相似题
     * @author demo 16-1-28
     */
    public function nextSimilarlyTest(){
        $id = $_GET['id'];
        $result = $this->getSimilarlyTest($id);
        $this->setBack($result['similarity']);
    }

    /**
     * 获取相似题
     * @author demo 16-1-28
     */
    private function getSimilarlyTest($id){
        $field = array(
            'testid', 'test','diff', 'subjectid', 'typesid','typesname', 'testnum', 'numbid','docname', 'klid','gradeid', 'kllist'
        );
        $result=$this->getModel('TestRealQuery')->getIndexTest($field,array('TestID'=>$id),array('testid asc'),array('perpage'=>150),0,-1);

        if(empty($result)){
            $this->setError('1X2018', $this->isAjax, U('Doc/Doc/show'));
        }

        $result = $result[0];
        $arr['TypesID']=$result['typesid'];
        $arr['KlID']=implode(',',$result['klid']);
        $arr['GradeID']=$result['gradeid'];
        $arr['SubjectID'] = $result['subjectid'];
        //此处仅从共有题库中取相关试题
        $similarity = $this->getModel('TestReal')->getTestIndex(array('testid','test'),$arr,array('@random'),array('page'=>1,'perpage'=>100,'limit'=>1));
        return array('test'=>$result, 'similarity' => (array)$similarity[0][0]);
    }

    /**
     * ajax获取试题 试题列表显示试题内容
     */
    public function getOneTestById() {
        //验证是否登陆
        $result=R('Common/PowerLayer/checkLogin',array('Home',1));
        if(is_numeric($result)){
            $result=R('Common/PowerLayer/checkLogin',array('Aat',1));
            if(is_numeric($result)){
                $this->setError($result,1);
            }
        }

        $output=array();
        $id = $_POST['id'];
        if(empty($id)) $this->setError('30301',1);
        $width = $_POST['width'];//未使用
        $where=array('TestID'=>$id);
        $field=array('kllist','analytic','answer','remark','firstloadtimeint','typeid');
        $page=array('page'=>1,'perpage'=>1);
        $tmpStr=R('Common/TestLayer/indexTestList',array($field,$where,'',$page));
        if($tmpStr === false){
            $this->setError('30504', (IS_AJAX ? 1 : 0));
        }

        //4.15后上传的同步测试类文档随机显示试题解析
        if($tmpStr[0][0]['loadtimeint']>strtotime('2015-4-15') && $tmpStr[0][0]['typeid']==7){
            //随机概率0.4
            $randInt=rand(100000, 999999);
            if($randInt<600000){
                $tmpStr[0][0]['analytic']='';
            }
        }

        if($tmpStr){
            $output[0]='success';
            $output[1]=$tmpStr;
            $this->setBack($output);
        }
    }
}