<?php
/**
 * @author demo 
 * @date 2014年12月16日
 * @update 2015年5月5日
 */
/**
 * 操作错误日志类，用于处理记录用户操作出现错误的相关操作
 */
namespace Manage\Controller;
class ErrLogController extends BaseController  {
    var $moduleName = '错误日志管理'; //模块名称
    /*
     * 浏览用户操作失败记录
     * @author demo 
     */
    public function index() {
        $pageName = '错误日志管理';
        $params['tableName'] = $_REQUEST['tableName'];//数据库表名
        if($_REQUEST['start']){
            $params['start'] = strtotime($_REQUEST['start']);
            if($params['start']  === false){
                $params['start'] = strtotime($_REQUEST['start'] = date('Y-m-d', $_REQUEST['start']));
            }
        }
        if($_REQUEST['end']){
            $params['end'] = strtotime($_REQUEST['end']);
            if($params['end']  === false){
                $params['end'] = strtotime($_REQUEST['end'] = date('Y-m-d', $_REQUEST['end']));
            }
        }
        $page = (int)$_REQUEST['p'];
        if($page == 0){
            $page = 1;
        }
        $prepage = 30;
        $data = $this->getModel('LogError')->getList($params, $page, $prepage); 
        $this->assign('fileDate', $data['data']);
        $this->pageList($data['total'],$prepage,$params);
        $this->assign('pageName', '错误日志管理');
        $this->display();
    }
    /**
     * 删除历史数据
     * @author demo 16-5-5 
     */
    public function del(){
        $this->getModel('LogError')->delData();
        $this->showsuccess('删除成功',__URL__);
    }
}