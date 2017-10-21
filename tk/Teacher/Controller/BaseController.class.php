<?php
/**
 * 基础控制器类，用于处理公共操作 该项目下所有操作数据库方法均继承该类
 */
namespace Teacher\Controller;
use Common\Controller\DefaultController;
class BaseController extends DefaultController{
    /**
     * 初始化类时运行的函数；检查用户是否在非公共页面中的登录情况；
     * @author demo
     */
    const NORMAL_ERROR = 0;
    const AJAX_ERROR = 1;
    const RETURN_ERROR = 2;
    public $defaultPower=array();

    public function __construct(){
        parent::__construct();
    }

    protected function _initialize() {
    }

    /**
     * 输出错误信息并跳转
     * @param string $msg_detail 提示信息
     * @param string $link 跳转地址 默认上一页
     * @param bool $auto_redirect 是否自动跳转
     * @param int $seconds 跳转时间
     * @author demo
     * */
    public function showerror($msg_detail, $link='', $auto_redirect = true, $seconds = 3) {
        $this->showError($msg_detail, $link, $auto_redirect, $seconds);
    }

    /**
     * 数据分页
     * @param int $count 总数
     * @param int $prepage 每页数量
     * @param array $map 参数数组 用于跳转到分页时带查询参数 格式为array('key'=>'value')
     * @author demo 
     * */
    public function pageList($count,$prepage,$map=array()){
        $page = handlePage('init',$count,$prepage);
        if($map){
            foreach($map as $key=>$val) {
                $page->parameter   .=   "$key=".urlencode($val).'&';
            }
        }
        $show = $page->show();
        $this->assign('page', $show); // 赋值分页输出
    }
    /**
     * 写入系统日志
     * @param string $module 模块名称
     * @param string $content 日志内容
     * @author demo
     * */
    public function teacherLog($module,$content){
        $this->userLog($module,$content);
    }

    /**
     * 公共数据(多个方法需要此处数据)
     * @return string 权限列表ID字符串
     * @author demo
     * @date 2014年11月16日
     */
    protected function publicInfo(){
        $userName = $this->getCookieUserName();
        if(empty($userName)){
            $loginUrl = U('Teacher/Public/login');
            $this->setError('30204',0,$loginUrl);
        }
        return R('Common/PowerLayer/teacherPublicInfo',array($userName));
    }

    /*
     * ajax获取基本信息
     * @author demo
     */
    public function getBasicData(){
        extract($_POST);
        if(isset($pID) && $pID != 0){
            $this->setBack($this->getData(array('subjectID'=>$subject,'style'=>$style,'pID'=>$pID,'return'=>2)));
        }else{
            $l = explode(',',$style);
            $data = array();
            foreach($l as $value){
                $id = '';
                switch($value){
                    case 'knowledgeList':{
                        $id = $klid;
                    }
                    break;
                    case 'chapterList':{
                        $id = $clid;
                    }
                    break;
                }
                $data[$value] = $this->getData(array('subjectID'=>$subject,'style'=>$value,'ID'=>$id,'return'=>2));
            }
            $this->setBack($data);
        }
        
    }

    /**
     * 写入日志记录
     * @param string $testID 试题ID
     * @param string $status 状态描述
     * @param string|array $userName 用户名
     * @param string $content 描述
     * @param int $ifTotal 记录是否参与统计
     * @param int $addPoint 给出的分值
     * @author demo
     */
    protected function customLog($testID,$status='',$userName,$content,$ifTotal=0,$addPoint=0) {
        $logData=array();
        $logData['TestID']=$testID;
        $logData['Status']=$status;
        if(is_array($userName)){
            $logData['UserName']=$userName['UserName'];
            $logData['Admin'] = $userName['Admin'];
        }else{
            $logData['UserName']=$userName;
        }
        $logData['Point']=$addPoint;
        $logData['Description']=$content;
        $logData['AddTime']=time();
        $logData['IfTotal']=$ifTotal;
        $this->getModel('CustomTestTaskLog')->insertData(
            $logData
        );
    }

    /**
     * 获取知识点内容
     * @param string $table 要查询的数据库
     * @param int $testID
     * @return array
     * @author demo
     */
    public function getKnowledgeInfo($table,$testID){
        $buffer = $this->getModel($table)->selectData(
            '*',
            'TestID='.$testID);
        if($buffer){
            $result=array();
            foreach($buffer as $iBuffer){
                $param['style']='knowledgeList';
                $param['ID']=$iBuffer['KlID'];
                $param['return']=2;
                $result['KlID'].=','.$iBuffer['KlID'];
                $result['knowledge'][]=$this->getData($param)[0];
            }
            $result['KlID']=substr($result['KlID'],1);
            return $result;
        }
    }

    /**
     * 根据试题id获取章节内容
     * @param string $table 查询的数据表
     * @param int $testID 试题编号
     * @return array
     * @author demo
     */
    public function getChapterInfo($table,$testID){
        $buffer = $this->getModel($table)->selectData(
            '*',
            'TestID='.$testID);
        if($buffer){
            $result=array();
            foreach($buffer as $iBuffer){
                $param['style']='chapterList';
                $param['ID']=$iBuffer['ChapterID'];
                $param['return']=2;
                $result['ChapterID'].=','.$iBuffer['ChapterID'];
                $result['chapter'][]=$this->getData($param)[0];
            }
        }
        $result['ChapterID']=substr($result['ChapterID'],1);
        return $result;
    }


    /**
     * 获取选项宽度； 
     * @return array
     * @author demo
     */
    public function getOptionWidth(){
        $testData=$_POST['testData'];
        $test=$this->getModel('Test');
        $arr=$test->getOptionWidth($testData);
        if(!$arr){
            $output=array(array(0,0));
        }else{
            $output=$arr;
        }
        $this->setBack($output);
    }

    /**
     * ajax获取知识点对应章节 get方式
     * @param string kl 知识点 1,2,3
     * @return json 章节数据集
     * @author demo
     */
    public function getchapter(){
        $kl=$_GET['kl']; //知识点ID

        if(!$kl) {
            $this->setError('30301',1);
        }
        $buffer=$this->getModel('ChapterKl')->selectData(
            '*',
            'KID in ('.$kl.')'
        );
        if(!$buffer){
            $this->setError('40602',1);
        } 
        $idlist=array();
        foreach($buffer as $buffern){
            $idlist[]=$buffern['CID'];
        }
        $idlist=array_unique($idlist);
        
        $Chapter=$this->getModel('Chapter');
        $idlist=$Chapter->filterChapterID($idlist);
        $buffer=array();
        if($idlist){
            $buffer=$this->getModel('Chapter')->selectData(
                'ChapterID',
                'ChapterID in ('.implode(',',$idlist).')',
                'ChapterID asc'
            );
            $result = array();
            $cache = D( 'ApiCache');
            foreach($buffer as $value){
                $data = $cache->chapterListCache(array(
                    'ID'=>$value['ChapterID']
                ));
                $result[] = $data[0];
            }
            // $bufferx=SS('chapterParentPath');
            // foreach($buffer as $kk=>$buffern){
            //     $output='';
            //     if($bufferx[$buffern['ChapterID']]){
            //         krsort($bufferx[$buffern['ChapterID']]);
            //         foreach($bufferx[$buffern['ChapterID']] as $a){
            //             $output.='>>'.$a['ChapterName'];
            //         }
            //             $output.='>>'.$buffern['ChapterName'];
            //         $buffer[$kk]['ChapterName']=$output;
            //     }else{
            //             $output='>>'.$buffern['ChapterName'];
            //     }
            // }
            // exit(dump($buffer));
        }
        $this->setBack($result);
    }


    /**
     * 获取下一个试题编号
     * @return int 下一个id
     * @author demo
     */
    protected function getNextId($arr,$id){
        $next = 0;
        foreach($arr as $key=>$value){
            if($value['TestID'] == $id){
                $next = $key;
                continue;
            }
        }
        if(++$next >= count($arr)){
            return -1;
        }
        return $arr[$next]['TestID'];
    }
}
?>