<?php
/**
 * @author demo
 * @date 2015年4月20日
 */
/**
 *校本题库之操作日志 操作类
 */
namespace Teacher\Controller;
class CustomTestLogController extends BaseController{
    //用户名
    private $userName="";

    public function __construct(){
        parent::__construct();
        $this->userName=$this->getCookieUserName();
    }

   /**
    * 日志列表
    * @author demo
    */ 
    public function index(){
        $pageName= '日志列表';
        $map=array();
        $data='a.UserName="'.$this->userName.'" AND b.SubjectID='.$this->mySubject;
        if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
            //简单查询
            $map['TestID'] = $_REQUEST['name'];
            $data .= ' and a.TestID='.$_REQUEST['name'];
        } else {
            //高级查询
            if ($_REQUEST['TestID']) {
                $map['TestID'] = $_REQUEST['TestID'];
                $data .= ' and a.TestID='.$_REQUEST['TestID'];
            }
            $start = $_REQUEST['Start'];
            $end = $_REQUEST['End'];
            if ($start) {
                if(!checkString('isDate',$_REQUEST['Start'])){
                    $start = date('Y-m-d', $start);
                }
                if(!checkString('isDate',$_REQUEST['End'])){
                    $end = date('Y-m-d', $end);
                }
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->setError('30719'); //日期格式不正确
                }
                $map['Start'] = strtotime($start);
                $map['End'] = strtotime($end);
                $data .= ' AND a.AddTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
        }
        //载入分页类
        $perPage = C('WLN_PERPAGE'); //每页 页数
        $CustomTestTaskLog = $this->getModel('CustomTestTaskLog');
        /*校本题库审核日志统计总数*/
        $count=$CustomTestTaskLog->unionSelect('customTestTaskLogSelectCount',$data);
        $this->pageList($count, $perPage, $map);
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        /*校本题库审核日志分页浏览*/
        $list=$CustomTestTaskLog->unionSelect('customTestTaskLogSelectByPage',$data,$page);
        $subject = SS('subject');
        /*处理记录显示内容*/
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
            if($iList['Admin']==''){
                $list[$i]['Admin']='系统';
            }
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
}