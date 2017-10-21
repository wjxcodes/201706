<?php
/**
 * @author demo  
 * @date 2014年11月28日 2015-1-7
 * @update 2015-11-15
 */
/**
 * 后台订单类，用处理订单配置相关操作
 */
namespace Manage\Controller;
class OrderListController extends BaseController  {
    var $moduleName = '订单管理';
    /**
     * 浏览
     * @author demo 
     */
    public function index() {
        $pageName = '订单管理';
        $map=array();
        $data=' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND OrderName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['OrderNum']){//订单号
                if(is_numeric($_REQUEST['OrderNum'])) {
                    $map['OrderNum'] = $_REQUEST['OrderNum'];
                    $data .= ' AND OrderID like "%'.$_REQUEST['OrderNum'].'%" ';
                }else{
                    $this->setError('30502');
                }
            }
            if($_REQUEST['UserID']){
                $map['UserID']=$_REQUEST['UserID'];
                $data.=' AND UID = "'.$_REQUEST['UserID'].'" ';
            }
            $start = trim($_REQUEST['Start']);
            $end = trim($_REQUEST['End']);
            if ($start) {
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->showError('日期格式不正确');
                }
                $map['Start'] = $_REQUEST['Start'];
                $map['End'] = $_REQUEST['End'];
                $data .= ' AND OrderTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
            if(is_numeric($_REQUEST['Status'])){
                $map['Status']=$_REQUEST['Status'];
                $data.=' AND OrderStatus = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage=C('WLN_PERPAGE');
        $orderList=$this->getModel('OrderList');
        $count = $orderList->selectCount(
            $data,
            'OLID'
            ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list = $orderList->pageData(
            '*',
            $data,
            'OLID DESC',
            $page
        );
        $this->pageList($count,$perpage,$map);

        /*载入模板标签*/
        $this->assign('list', $list); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 查看信息
     * @author demo
     */
    public function view() {
        $pageName='查看订单';
        $orderID=$_GET['id'];
        if(empty($orderID)){
            $this->setError('30301'); //数据标识不能为空
        }
        $edit=$this->getModel('OrderList')->selectData(
            '*',
            'OLID='.$orderID);
        $this->assign('edit', $edit[0]); //菜单数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 更改订单状态和备注
     * @author demo
     */
    public function save() {
        $data=array();
        $data['OLID']=$_POST['OLID'];
        $data['OrderStatus']=$_POST['Status'];
        $data['Remark']=$_POST['Remark'];
        if(empty($data['OLID'])){
            $this->setError('30301'); //数据标识不能为空
        }
        if($this->getModel('OrderList')->updateData(
                $data,
                'OLID='.$data['OLID'])===false){
            $this->setError('30311'); //修改失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'修改订单OLID为【'.$_POST['OLID'].'】的数据');
            $this->showSuccess('修改成功！', __URL__);
        }
    }
}