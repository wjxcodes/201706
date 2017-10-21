<?php
/**
 * @author demo
 * @date 2014年10月24日
 * @update 2015年2月6日
 */
/**
 * 焦点图轮换管理类
 */

namespace Index\Manage;
class ImagePlayManage extends BaseController
{
    var $moduleName = '焦点图管理';
    /**
     * 焦点图列表
     * @author demo
     */
    public function index()
    {
        $pageName = '焦点图列表';
        $where='1=1';
        $map=array();
        if($_REQUEST['name']){
            $where.=' AND Target like "%'.$_REQUEST['name'].'%"';
            $map['Target']=$_REQUEST['name'];
        }
        // 查询满足要求的总记录
        $imagePlay = $this->getModel('ImagePlay');
        $count = $imagePlay->selectCount(
            $where,
            'ImageID');
        $perpage = C('WLN_PERPAGE');
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage). ',' . $perpage;
        $list = $imagePlay->pageData(
            '*',
            $where,
            'Target ASC,OrderID ASC',
            $page);
        $this->pageList($count, $perpage, $map);
        $host=C('WLN_DOC_HOST');
        if($host)
            foreach($list as $i=>$iList){
                $list[$i]['Url']=$host.$iList['Url'];
            }
        $this->assign('list', $list);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加焦点图
     * @author demo
     */
    public function add()
    {
        $pageName = '添加焦点图';
        $act = 'add'; //模板标识
        //获取分组
        $group = $this->getModel('ImagePlay')->distinctData(
            'Target',
            '1=1'
        );

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('group', $group); //分组数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑焦点图
     * @author demo
     */
    public function edit()
    {
        $imageID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($imageID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑焦点图';
        $act = 'edit'; //模板标识
        $imagePlay =   $this->getModel('ImagePlay');
        $edit = $imagePlay->selectData(
            '*',
            'ImageID="' . $imageID . '"');
        if ($this->ifDiff && $edit[0]['Admin'] != $this->getCookieUserName()) {
            $this->setError('30812'); //您没有编辑此焦点图的权限
        }
        //获取分组
        $group =$imagePlay->distinctData(
            'Target',
            '1=1');

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('group', $group); //分组数据集
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('ImagePlay/add');
    }
    /**
     * 保存焦点图
     * @author demo
     */
    public function save()
    {
        $imageID = $_POST['ImageID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($imageID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $data = array();
        $data['Title'] = $_POST['Title'];
        $data['Description'] = formatString('stripTags',$_POST['Description']);
        $data['Admin'] = $this->getCookieUserName();
        $data['Target'] = $_POST['target'];
        $data['OrderID'] = $_POST['OrderID'];

        if ($_FILES['imgName']['size']) { //如果修改图片
            $path = R('Common/UploadLayer/uploadImg'); //上传图片
            if(strstr($path,'error')){
                $this->setError('30725',0,'',$path);
            }
            $data['Url']=$path;
        }

        if ($act == 'add') {
            if ($this->getModel('ImagePlay')->insertData(
                    $data) === false) {
                $this->setError('30310'); //添加失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '添加焦点图【' . $_POST['Title'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
            $data['ImageID'] = $imageID;
            $data['Title'] = $_POST['Title'];
            $data['Description'] = formatString('stripTags',$_POST['Description']);
            $data['Admin'] = $this->getCookieUserName();
            $data['Target'] = $_POST['target'];
            $data['OrderID'] = $_POST['OrderID'];
            
            if ($this->getModel('ImagePlay')->updateData(
                    $data,
                    'ImageID='.$imageID) === false) {
                $this->setError('30311'); //修改失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '修改焦点图ImageID为【' . $imageID . '】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }
    /**
     * 删除焦点图
     * @author demo
     */
    public function delete()
    {
        $imageID = $_POST['id']; //获取数据标识
        if (!$imageID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $arr = array(); //存放要删除焦点图的路径
        $buffer = $this->getModel('ImagePlay')->selectData(
            '*',
            'ImageID in (' . $imageID . ')'
        );
        if (!$buffer) {
            $this->setError('80114','',__URL__); //没有要删除的数据！
        }
        //把要删除的焦点图路径赋值给数组$arr
        foreach ($buffer as $i => $iBuffer) {
            $arr[$i] = $iBuffer['Url'];
        }
        foreach ($arr as $i => $iArr) {
            unlink($iArr);
        }
        if ($buffer = $this->getModel('ImagePlay')->deleteData(
                'ImageID in('.$imageID.')') === false) {
            $this->setError('30302','',__URL__); //删除失败！
        }
        //写入日志
        $this->adminLog($this->moduleName, '删除焦点图ImageID为【' . $imageID . '】的数据');
        $this->showSuccess('删除成功！', __URL__);
    }
}