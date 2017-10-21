<?php
/**
 * @author demo
 * @date 2015年1月9日
 */
/**
 * 反馈信息控制器类，用于反馈信息相关操作
 */
namespace Index\Manage;
class WlnFeedbackManage extends BaseController  {
    var $moduleName = '反馈记录';
    var $from=array('home'=>'组卷',
                           'aat'=>'提分',
                           'aatMobile'=>'手机端',
                           'index'=>'官网'
        );//信息反馈来源
    var $style=array('0'=>'申请开通',
                     '1'=>'留言反馈'
        );//信息反馈类型
    var $openStyle=array('name'=>'Excel名单',
                     'personal'=>'个人申请',
                     'ip'=>'公网IP',
                     'recvbook'=>'送书'
        );//信息反馈类型
    /**
     * 浏览反馈信息
     * @author demo
     */
    public function index() {
        $pageName = '反馈记录';
        //高级查询
        $where=$this->getWhere($_REQUEST);
        $perpage=C('WLN_PERPAGE');
        $feedback = $this->getModel('Feedback');
        $count = $feedback->selectCount(
            $where['data'],
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $feedback->pageData(
            '*',
            $where['data'],
            'FeedbackID DESC',
            $page);
        $host=C('WLN_DOC_HOST');
        if($list){
            foreach($list as $i=>$iList){
                $list[$i]['ShowLink']=0;
                $list[$i]['Style']=$this->style[$iList['Style']];
                $list[$i]['From']=$this->from[$iList['From']];
                if($iList['Style']!=1){
                    $list[$i]['OpenStyle']= isset($this->openStyle[$iList['OpenStyle']]) ? '【'.$this->openStyle[$iList['OpenStyle']].'】' : '';
                }else{
                    if($list[$i]['OpenStyle']!=0){
                        $list[$i]['ShowLink']=1;
                    }else{
                        $list[$i]['OpenStyle']='';
                    }
                }

                $schoolName = explode('<br>',$iList['Content'])[0];//<br>和index/feedback对应
                $schoolName = explode('：',$schoolName)[1];//：和index/feedback对应
                if($iList['FilePath']){
                    $list[$i]['url']=$host.R('Common/UploadLayer/getDocServerUrl',array($iList['FilePath'],'down','bbs',$schoolName."教师名单"));
                }
            }
        }
        $this->pageList($count,$perpage,$where['map']);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('from', $this->from); // 来源
        $this->assign('style', $this->style); // 信息类型
        $this->assign('openStyle', $this->openStyle); // 开通方式
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 下载反馈记录附件
     * @author demo
     */
    public function downExcel(){
        $id=$_GET['id'];
        $list = $this->getModel('Feedback')->selectData(
            'Content,FilePath',
            'FeedbackID='.$id
        );
        $host=C('WLN_DOC_HOST');
        foreach($list as $i=>$iList){
            $schoolName = explode('<br>',$iList['Content'])[0];//<br>和index/feedback对应
            $schoolName = explode('：',$schoolName)[1];//：和index/feedback对应
            if($iList['FilePath']){
                $url=$host.R('Common/UploadLayer/getDocServerUrl',array($iList['FilePath'],'down','bbs',$schoolName."教师名单"));
            }
        }
        //写入日志
        $this->adminLog($this->moduleName,'下载反馈记录FeedbackID为【'.$id.'】的附件数据');
        header('Location:'.$url);
    }

    /**
     * 修改反馈信息状态
     * @author demo
     */
    public function check() {
        $id=$_GET['id'];
        if (empty ($id)) {
            $output='30301';
            $this->setError($output,1); //alert("数据标识不能为空！")
        }else{
            $data['Status']=$_GET['status'];
            if ($this->getModel('Feedback')->updateData(
                    $data,
                    'FeedbackID='.$id) === false) {
                $this->setError("30311",1);
            }else{
                $this->setBack('success');
            }
        }
        
    }
    /**
     * 删除反馈信息
     * @author demo
     */
    public function delete() {
        $id = $_POST['id']; //获取数据标识
        if (!$id) {
             $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('Feedback')->deleteData(
            'FeedbackID in ('.$id.')') === false) {
            $this->setError('30302','',__URL__); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除反馈信息FeedbackID为【'.$id.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     *@用户反馈记录导出
     *@author demo
     */
    public function export() {
        $where=$this->getWhere($_REQUEST);
        //写入日志
        $this->adminLog($this->moduleName,'导出用户反馈日志记录where【'.$where['data'].'】');
        $feedback = $this->getModel('Feedback');
        $count = $feedback->selectCount(
            $where['data'],
            'FeedbackID'); // 查询满足要求的总记录
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $feedback->selectCount(
                $where['data'],
                'FeedbackID'); // 查询满足要求的总记录
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $page=R('Common/SystemLayer/exportPageList',array($count,$perpage,$where['map']));
            $result['data']=$page;
            $result['ifPage']=1;
            $this->setBack($result);

        }
        if(!$_GET['p']){
            $this->setBack('');
        }
        if($_GET['p']=='all'){
            $_GET['p']=0;
        }
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage; //分页条件
        $subjectArray=SS('subject');
        $logData = $feedback->pageData(
            'LoadTime,UserName,Content',
            $where['data'],
            'FeedbackID desc',
            $page);
        foreach ($logData as $i => $value) {
            $logData[$i]['SchoolName']='';
            $logData[$i]['PhoneNum']='';
            $logData[$i]['Email']='';
            $logData[$i]['Address']='';
            $logData[$i]['TextContent']='';
            $logData[$i]['IP']='';
            $logData[$i]['subjectName']='';
            $logData[$i]['sectionName']='';
            $logData[$i]['chapterName']='';
            $newArray=preg_split('/<br[^>]*>/i',$value['Content']); //分割content，获取相关信息
            foreach($newArray as $j=>$jNew){
                if(strpos($newArray[$j],'名：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['SchoolName']=$arr[1];
                }elseif(strpos($newArray[$j],'话：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['PhoneNum']=$arr[1];
                }elseif(strpos($newArray[$j],'箱：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['Email']=$arr[1];
                }elseif(strpos($newArray[$j],'址：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['Address']=$arr[1];
                }elseif(strpos($newArray[$j],'容：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['TextContent']=$arr[1];
                }elseif(strpos($newArray[$j],'箱/电话/QQ：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['Email']=$arr[1];
                }elseif(strpos($newArray[$j],'P：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['IP']=$arr[1];
                }elseif(strpos($newArray[$j],'科：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['subjectName']=$arr[1];
                }elseif(strpos($newArray[$j],'本：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['sectionName']=$arr[1];
                }elseif(strpos($newArray[$j],'节：')){
                    $arr=explode('：',$newArray[$j]);
                    $logData[$i]['chapterName']=$arr[1];
                }
            }
            unset($logData[$i]['Content']);
            $logData[$i]['LoadTime']=date('Y-m-d H:i:s', $value['LoadTime']);
            if(empty($logData[$i]['IP'])){
                $logData[$i]['IP']='';
            }
            if(empty($logData[$i]['Email'])){
                $logData[$i]['Email']='';
            }
            $logData[$i]=array_values($logData[$i]);
        }

        $keyName=array('申请时间','用户名','学校名称','电话','邮箱','地址','内容','IP','学科','版本','章节');
        $keyWidth=array('20','20','30','20','20','50','20','50','20','20','20');
        $excelName=array('title'=>'用户反馈记录日志列表','excelName'=>'用户反馈记录信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$logData,$excelName));
    }
    /**
     * 获取分页条件
     */
    private function getWhere($where){
        $map = array ();
        $data = ' 1=1 ';
        $map=array();
        //高级查询
        if($_REQUEST['from']){
            $data.=' AND `From`="'.$_REQUEST['from'].'"';
        }
        if($_REQUEST['style']!= ''){
            $data.=' AND Style='.$_REQUEST['style'];
        }
        if($_REQUEST['Status']!= ''){
            $data.=' AND Status='.$_REQUEST['Status'];
            $map['Status'] = $_REQUEST['Status'];
        }
        if($_REQUEST['openStyle']!= ''){
            $data.=' AND OpenStyle="'.$_REQUEST['openStyle'].'"';
            $map['OpenStyle'] = $_REQUEST['openStyle'];
        }
        $start = $_REQUEST['Start'];
        if(strstr($start,'-')){
            $start=strtotime($start);
        }
        $end = $_REQUEST['End'];
        if(strstr($end,'-')){
            $end=strtotime($end);
        }
        if ($start){
            if (empty ($end)) $end = time();
            $map['Start'] = $start;
            $map['End'] = $end;
            $_REQUEST['Start']=date('Y-m-d',$start);
            $_REQUEST['End']=date('Y-m-d',$end);
            $data .= ' AND LoadTime between ' . ($start) . ' and ' . ($end) . ' ';
        }
        $where['map']=$map;
        $where['data']=$data;
        return $where;
    }
}