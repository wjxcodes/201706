<?php
/**
 * @author demo  
 * @date 2014年11月28日 2015-1-7
 */
/**
 * 自定义标签管理，用处理自定义相关操作
 */
namespace Index\Manage;
class MyTagManage extends BaseController  {
    var $moduleName = '自定义标签管理';
    /**
     * 浏览浏览列表；
     * @author demo
     */
    public function index() {
        $pageName = '自定义标签管理';
        $myTagObj = $this->getModel('MyTag');
        $typeListArray =$myTagObj->distinctData(
            'Type');
        $map=array();
        $data=' 1=1 ';
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND Title like "%'.$_REQUEST['name'].'%" ';
            }else{
                //高级查询
                if($_REQUEST['Title']){
                    $map['Title']=$_REQUEST['Title'];
                    $data.=' AND Title like "%'.$_REQUEST['Title'].'%" ';
                }
                if($_REQUEST['TagName']){
                    $map['TagName']=$_REQUEST['TagName'];
                    $data.=' AND TagName like "%'.$_REQUEST['TagName'].'%" ';
                }
                if($_REQUEST['Type']){
                    $map['Type']=$_REQUEST['Type'];
                    $data.=' AND Type = "'.$_REQUEST['Type'].'" ';
                }
            }
        $perpage=C('WLN_PERPAGE');
        $count = $myTagObj->selectCount(
            $data,
            'TagID'
        ); // 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $myTagObj->pageData(
            '*',
            $data,
            'TagID desc',
            $page);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('typeListArray', $typeListArray); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加
     * @author demo
     */
    public function add() {
        $pageName = '添加自定义标签';
        $act = 'add'; //模板标识
        $typeListArray = $this->getModel('MyTag')->distinctData(
            'Type'
        );
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('typeListArray', $typeListArray); //父类数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑
     * @author demo
     */
    public function edit() {
        $TagID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($TagID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑自定义标签';
        $act = 'edit'; //模板标识
        $myTagObj = $this->getModel('MyTag');
        $typeListArray = $myTagObj->distinctData(
            'Type');
        $edit = $myTagObj->selectData(
            '*',
            'TagID='.$TagID);
        $edit[0]['Content']=R('Common/TestLayer/strFormat',array($edit[0]['Content']));
        $jsonEdit=json_encode( $edit[0]);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('data', $jsonEdit);
        $this->assign('edit', $edit[0]);
        $this->assign('typeListArray', $typeListArray);
        $this->assign('pageName', $pageName);
        $this->display('MyTag/add');
    }
    /**
     * 保存
     * @author demo
     */
    public function save() {
        $TagID = $_POST['TagID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($TagID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $data = array ();
        $data['Title'] = $_POST['Title'];
        $data['TagName'] = $_POST['TagName'];
        $data['Description'] = $_POST['Description'];
        $newContent=formatString('IPReplace',$_POST['Content']);
        $data['Content'] = stripslashes_deep($newContent);//修改 2014/8/18
        $data['Type'] = $_POST['Type'];
        $data['LoadTime'] = time();
        $data['Admin'] = $this->getCookieUserName();
        $myTagObj = $this->getModel('MyTag');
        if ($act == 'add') {
            //检查自定义标签名称重复
            $buffer = $myTagObj->selectData(
                '*',
                'TagName="'.$data['TagName'].'"');
            if($buffer){
                $this->setError('80115'); //标签名称重复请更换！
                exit;
            }
            if($myTagObj->insertdata(
                    $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加自定义标签【'.$_POST['TagName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else
            if ($act == 'edit') {
                $data['TagID'] = $_POST['TagID'];
                if($myTagObj->updateData(
                        $data,
                        'TagID ='.$data['TagID'])===false){
                    $this->setError('30311'); //修改失败！
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'修改自定义标签TagID为【'.$_POST['TagID'].'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除
     * @author demo
     */
    public function delete() {
        $tagID = $_POST['id']; //获取数据标识
        if (!$tagID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $myTagObj = $this->getModel('MyTag');
        if ($myTagObj->deleteData(
                'TagID in ('.$tagID.')')=== false) {
            $this->setError('30301'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除自定义标签AdminID为【'.$tagID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}