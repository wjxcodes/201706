<?php
/**
 * @author demo
 * @date 2015年7月21日
 */
/**
 * 版本更新管理控制器类
 */
namespace Manage\Controller;
class SystemEditionLogController extends BaseController  {

    /**
     * 版本更新列表
     * @author demo
     */
    public function index(){
        $pageName='版本更新列表';
        $perpage = C('WLN_PERPAGE');
        $adminName=$this->getCookieUserName();
        $map = array ();
        $data='1=1';
        if ($_REQUEST['name']) {
            if($this->ifDiff && $adminName!=$_REQUEST['name']){
                $this->setError('30313'); //您没有权限查看该内容！
                return;
            }
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND EditionNum like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['EditionNum']) {
                $map['EditionNum'] = $_REQUEST['EditionNum'];
                $data .= ' AND EditionNum = "' . $_REQUEST['EditionNum'] .' "';
            }
            if (is_numeric($_REQUEST['IfNoMain'])) {
                $map['IfNoMain'] = $_REQUEST['IfNoMain'];
                $data .= ' AND IfNoMain ="' . $_REQUEST['IfNoMain'] . '" ';
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
                $data .= ' AND ShowTime between ' . ($start) . ' and ' . ($end) . ' ';
            }
        }
           $systemEditionLog=$this->getModel('SystemEditionLog');
        $count=$systemEditionLog->selectCount(
            $data,
            '*'
        );
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list=$systemEditionLog->pageData(
            '*',
            $data,
            'AddTime Desc',
            $page
        );
        $this->pageList($count, $perpage, $map);
        $this->assign('list',$list);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 版本编辑
     * @author demo
     */
    public function edit(){
        $editionID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($editionID)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑公告';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('SystemEditionLog')->selectData(
            '*',
            'EditionID="'.$editionID.'"',
            '',
            '1');
        if($this->ifDiff && $edit[0]['Admin'] != $this->getCookieUserName()){
            $this->setError('30812');//您没有编辑权限!
        }
        $edit[0]['Content']=R('Common/TestLayer/strFormat',array($edit[0]['Content']));
        $edit[0]['ShowTime']=date('Y-m-d',$edit[0]['ShowTime']);
        $jsonEdit=json_encode($edit[0]);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('data', $jsonEdit);
        $this->assign('pageName', $pageName);
        $this->display('SystemEditionLog/add');
    }


    /**
     * 版本内容添加页
     * @author demo
     */
    public function add(){
        $pageName = '添加版本';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 版本内容修改页
     * @author demo
     */
    public function save(){
        $newContent=formatString('IPReplace',$_POST['Content']);
        $EditionID = $_POST['EditionID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($EditionID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array ();
        $data['EditionNum'] = $_POST['EditionNum'];
        $data['Content'] = $newContent;
        $data['EditionNum'] =$_POST['EditionNum'];
        $data['Admin'] = $this->getCookieUserName();
        $data['IfNoMain'] = $_POST['IfNoMain'];
        $data['ShowTime'] =strtotime($_POST['ShowTime']);
        $systemEditionLog=$this->getModel('SystemEditionLog');
        if ($act == 'add') {
            $data['AddTime'] = time();
            $data['EditTime'] = time();
            if($systemEditionLog->insertData(
                    $data) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加公告【'.$_POST['EditionNum'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else if ($act == 'edit') {
            $data['EditTime']=time();
            if($systemEditionLog->updateData(
                    $data,
                    'EditionID = "'.$EditionID.'"')===false){
                $this->setError('30311');//修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改公告EditionID为【'.$EditionID.'】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }

    /**
     * 删除出版本
     * @author demo
     */
    public function delete(){
        $EditionID = $_POST['id']; //获取数据标识
        if (!$EditionID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('SystemEditionLog')->deleteData(
                'EditionID in ('.$EditionID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除日志记录EditionID:'.$EditionID.'');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}