<?php
/**
 * @author demo
 * @date 2014年10月14日
 */
/**
 * 公告管理控制器类
 */
namespace Manage\Controller;
class NewsController extends BaseController  {
    var $moduleName = '公告管理';
    /**
     * 浏览公告列表
     * @author demo
     */
    public function index() {
        $pageName = '公告管理';
        $news = $this->getModel('News');
        $map = array();
        $data = ' 1=1 ';
        if($this->ifDiff){
            $data .= ' AND Admin = "'.$this->getCookieUserName().'"';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND NewTitle like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['NewTitle']){
                $map['NewTitle'] = $_REQUEST['NewTitle'];
                $data .= ' AND NewTitle like "%'.$_REQUEST['NewTitle'].'%" ';
            }
            if(is_numeric($_REQUEST['Status'])){
                $map['Status'] = $_REQUEST['Status'];
                $data.=' AND Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage = C('WLN_PERPAGE');
        $count = $news->selectCount(
            $data,
            "*"); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list = $news->pageData(
            '*',
            $data,
            'NewID DESC',
            $page);
        $this->pageList($count,$perpage,$map);
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加公告
     * @author demo
     */
    public function add() {
        $pageName = '添加公告';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑公告
     * @author demo
     */
    public function edit() {
        $newID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($newID)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑公告';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('News')->selectData(
            '*',
            'NewID="'.$newID.'"',
            '',
            '1');
        $edit[0]['NewContent']=R('Common/TestLayer/strFormat',array($edit[0]['NewContent']));
        if($this->ifDiff && $edit[0]['Admin'] != $this->getCookieUserName()){
            $this->setError('30301');//您没有编辑此公告的权限!
        }
        $jsonEdit=json_encode( $edit[0]);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('data', $jsonEdit);
        $this->assign('pageName', $pageName);
        $this->display('News/add');
    }
    /**
     * 保存公告
     * @author demo
     */
    public function save() {
        $newContent=formatString('IPReplace',$_POST['NewContent']);
        $newID = $_POST['NewID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($newID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }
        $news = $this->getModel('News');
        $data = array ();
        $data['NewTitle'] = $_POST['NewTitle'];
        $data['NewContent'] = $newContent;
        $data['Admin'] = $this->getCookieUserName();
        $data['Color'] = $_POST['Color'];
        $data['Status'] = $_POST['Status'];
        $data['Types'] = $_POST['Types'];
        if ($act == 'add') {
            $data['LoadDate'] = time();
            //检查公告名称重复
            $buffer =$news->selectData(
                '*',
                'NewTitle="'.$data['NewTitle'].'"');
            if($buffer){
                $this->setError('11401');//公告名称重复请更换！
                exit;
            }
            if($news->insertData(
                    $data) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加公告【'.$_POST['NewTitle'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else if ($act == 'edit') {
            $data['NewID'] = $_POST['NewID'];
            if($news->updateData(
                    $data,
                    'NewID = "'.$newID.'"')===false){
                $this->setError('30311');//修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改公告NewID为【'.$_POST['NewID'].'】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }
    /**
     * 删除公告
     * @author demo
     */
    public function delete() {
          $newID = $_POST['id']; //获取数据标识
            if (!$newID) {
                $this->setError('30301','',__URL__);//数据标识不能为空！
            }

            if ($this->getModel('News')->deleteData(
                    'NewID in ('.$newID.')') === false) {
                $this->setError('30302');//删除失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName,'删除公告NewID为【'.$newID.'】的数据');
                $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 根据需要更新已有公告内容中的图片数据(更新后可删除)
     * @author demo
     */
    public function updateContent(){
        $news = $this->getModel('News');
        $result=$news->selectData(
            '*',
            '1=1');
        $array=array();
        foreach($result as $iResult){
            $data['NewContent']=formatString('IPReplace',$iResult['NewContent']);//替换公共内容中的地址信息
            $bool=$news->updateData(
                $data,
                'NewID='.$iResult['NewID']);
            if(!$bool){
                $array[]=$iResult['NewID'];
            }
        }
        if(count($array)){
            $this->showSuccess('更新成功');
        }else{
            $this->setError('30311');
        }
    }
}