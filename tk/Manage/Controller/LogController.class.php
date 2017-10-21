<?php
/**
 * @author demo
 * @Date: 2015年1月8日
 */
/**
 * 日志管理组类，用于日志管理相关操作
 */
namespace Manage\Controller;
class LogController extends BaseController  {
    var $moduleName = '日志管理'; //模块名称

    /**
     *@日志浏览
     *@author demo
     */
    public function index() {
        $pageName = '日志管理';
        $perpage = C('WLN_PERPAGE');
        $where=$this->getWhere($_REQUEST);
        $log=$this->getModel('Log');
        $count = $log->selectCount(
            $where['data'],
            'LogID'); // 查询满足要求的总记录
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage). ',' . $perpage;
        $list = $log->pageData(
            '*',
            $where['data'],
            'LogID desc',
            $page);
        $this->pageList($count, $perpage, $where['map']);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     *@日志导出
     *@author demo
     */
    public function export() {
        $perpage = 2000;
        $where=$this->getWhere($_REQUEST);
        $log=$this->getModel('Log');
        $count = $log->selectCount(
            $where['data'],
            'LogID'); // 查询满足要求的总记录
        if($count>2000 && empty($_REQUEST['p'])){
            $count = $log->selectCount(
                $where['data'],
                'LogID'); // 查询满足要求的总记录
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
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage;
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $logData = $log->pageData(
            '*',
            $where['data'],
            'LogID desc',
            $page);
        foreach($logData as $i=>$iLogData){
            if( $iLogData['IfAdmin'] == 1){
                $iLogData['IfAdmin']='管理员';
            }else{
                $iLogData['IfAdmin']='普通用户';
            }
            $iLogData['UserName']=$iLogData['UserName'].'('.$iLogData['IfAdmin'].')';
            $iLogData['LoadDate']= date('Y-m-d H:i:s', $iLogData['LoadDate']);
            unset($iLogData['IfAdmin']);
            $logData[$i]=array_values($iLogData);
        }
        $keyName=array('编号','所属模块','用户名','内容','时间','ip地址');
        $keyWidth=array('10','20','20','50','20','20');
        $excelName=array('title'=>'系统日志列表','excelName'=>'日志信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$logData,$excelName));
    }
    /**
     * 日志删除
     * @author demo
     */
    public function delete() {
        $logID = $_POST['id']; //获取数据标识
        if (!$logID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $log=$this->getModel('Log');
        $logArray =$log->selectData(
            '*',
            'LogID in (' . $logID . ') and LoadDate between ' . (time() - 7 * 24 * 3600) . ' and ' . time() . '');
        if ($logArray) {
            $this->setError('30816'); //删除失败，请不要删除最近7天内的日志！
            exit;
        }

        if ($log->deleteData(
                'LogID in ('.$logID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除日志记录LogID:'.$logID.'');
            $this->showSuccess('删除成功！', __URL__);
        }
    }

    /**
     * 获取查询条件
     * @param $where array $_REQUEST 参数
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $adminName=$this->getCookieUserName();
        $map = array ();
        $data = ' 1=1 ';
        if($this->ifDiff){
            $data .=' and UserName="'.$adminName.'"';
        }
        if ($_REQUEST['name']) {
            if($this->ifDiff && $adminName!=$_REQUEST['name']){
                $this->setError('30313'); //您没有权限查看该内容！
                return;
            }
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                if($this->ifDiff && $adminName!=$_REQUEST['name']){
                    $this->setError('30313'); //您没有权限查看该内容！
                    return;
                }
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName like "%' . $_REQUEST['UserName'] . '%" ';
            }
            if ($_REQUEST['Content']) {
                $map['Content'] = $_REQUEST['Content'];
                $data .= ' AND Content like "%' . $_REQUEST['Content'] . '%" ';
            }
            if ($_REQUEST['Module']) {
                $map['Module'] = $_REQUEST['Module'];
                $data .= ' AND Module ="' . $_REQUEST['Module'] . '" ';
            }
            if (is_numeric($_REQUEST['IfAdmin'])) {
                $map['IfAdmin'] = $_REQUEST['IfAdmin'];
                $data .= ' AND IfAdmin ="' . $_REQUEST['IfAdmin'] . '" ';
            }
            $start = $_REQUEST['Start'];
            if(strstr($start,'-')){
                $start=strtotime($start);
            }
            $end = $_REQUEST['End'];
            if(strstr($end,'-')){
                $end=strtotime($end);
            }
            if ($start) {
                if (empty ($end)) $end = time();
                $map['Start'] = $start;
                $map['End'] = $end;
                $_REQUEST['Start']=date('Y-m-d',$start);
                $_REQUEST['End']=date('Y-m-d',$end);
                $data .= ' AND LoadDate between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }
}