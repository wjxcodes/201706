<?php
/**
 * @author demo
 * @Date: 2015年1月8日
 */
/**
 * 日志管理组类，用于日志管理相关操作
 */
namespace Manage\Controller;
class LogSmsController extends BaseController  {
    var $moduleName = '短信日志管理'; //模块名称

    /**
     *验证码日志浏览
     *@author demo
     */
    public function index() {
        $pageName = '短信日志管理';
        $perpage = C('WLN_PERPAGE');
        $where=$this->getWhere($_REQUEST);
        $logSms=$this->getModel('LogSms');
        $count = $logSms->getLogSmsCount($where['data']); // 查询满足要求的总记录
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $where['page']=page($count,$_GET['p'],$perpage). ',' . $perpage;
        $list = $logSms->getLogSmsByWhere($where);
        $this->pageList($count, $perpage, $where['map']);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 获取查询条件
     * @param $where array $_REQUEST 参数
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $map = array ();
        $data = '1=1';
        if ($_REQUEST['Receive']) {//接收方
            //简单查询
            $map['Receive'] = $_REQUEST['Receive'];
            $data .= ' AND Receive="'.$_REQUEST['Receive'].'"';
        } else {
            //高级查询
            if ($_REQUEST['Receive']) {//接收方
                $map['Receive'] = $_REQUEST['Receive'];
                $data .= ' AND Receive="'.$_REQUEST['Receive'].'"';
            }
            if (is_numeric($_REQUEST['Status'])) {//是否发送成功
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND a.Status="'.$_REQUEST['Status'].'"';
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
                $data .= ' AND AddTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }
}