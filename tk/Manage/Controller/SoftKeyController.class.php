<?php
/**
 * @author demo
 * @date 2014年12月12日
 */
/**
 * 加密锁控制器类，用于处理加密锁相关操作
 */
namespace Manage\Controller;
class SoftKeyController extends BaseController  {
    var $moduleName='加密锁管理'; //模块名称
    /**
     * 按条件浏览加密锁列表；
     * @author demo
     */
    public function index() {
        $pageName = '加密锁管理';
        $map=array();
        $data=' 1=1 ';
            if($_REQUEST['name']){
                //简单查询
                $map['name']=$_REQUEST['name'];
                $data.=' AND KeyNum = "'.$_REQUEST['name'].'" ';
            }else{
                //高级查询
                if($_REQUEST['KeyNum']){
                    if(is_numeric($_REQUEST['KeyNum'])){
                        $map['KeyNum']=$_REQUEST['KeyNum'];
                        $data.=' AND KeyNum = "'.$_REQUEST['KeyNum'].'" ';
                    }else{
                        $this->setError('30502');
                    }
                }
                if($_REQUEST['KeyValue']){
                    $map['KeyValue']=$_REQUEST['KeyValue'];
                    $data.=' AND KeyValue = "'.$_REQUEST['KeyValue'].'" ';
                }
                if($_REQUEST['UserName']){
                    $map['UserName']=$_REQUEST['UserName'];
                    $data.=' AND UserName ="'.$_REQUEST['UserName'].'" ';
                } 
                if(is_numeric($_REQUEST['IfAdmin'])){
                    $map['IfAdmin']=$_REQUEST['IfAdmin'];
                    $data.=' AND IfAdmin = "'.$_REQUEST['IfAdmin'].'" ';
                }
            }
        $perpage=C('WLN_PERPAGE'); //每页行数
           $softKey=$this->getModel('SoftKey');
        $count = $softKey->selectCount($data,'KeyID'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $softKey->pageData(
                '*',
                $data,
                'KeyID DESC',
                $page
                );

        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加加密锁数据
     * @author demo
     */
    public function add() {
        $pageName = '添加加密锁';
        $act = 'add'; //模板标识

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑加密锁数据
     * @author demo
     */
    public function edit() {
        $keyID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($keyID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑加密锁';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('SoftKey')->selectData('*','KeyID='.$keyID);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('SoftKey/add');
    }
    /**
     * 保存加密锁数据
     * @author demo
     */
    public function save() {
        $keyID = $_POST['KeyID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($keyID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $data = array ();
        $data['KeyValue'] = $_POST['KeyValue'];
        if (empty ($data['KeyValue'])) {
            $this->setError('13601'); //加密锁内容不能为空！
        }
        $data['Key'] = $_POST['Key'];
        if (empty ($data['Key'])) {
            $this->setError('13602'); //加密锁密钥不能为空！
        }
        $data['KeyNum'] = $_POST['KeyNum'];
        if (empty ($data['KeyNum'])) {
            $this->setError('13606'); //加密锁id不能为空！
        }
        $data['IfAdmin'] = $_POST['IfAdmin'];
        if (!is_numeric($data['IfAdmin'])) {
            $this->setError('13603'); //请选择是否是管理员！
        }
        $data['UserName'] = $_POST['UserName'];
        if (empty ($data['UserName'])) {
            $this->setError('13604'); //加密锁绑定用户不能为空！
        }

        $buffer=$this->getModel('User')->selectData('UserID','UserName="'.$data['UserName'].'"');
        if(!$buffer){
            $this->setError('13605'); //加密锁绑定用户不存在！',
              }
        $softKey=$this->getModel('SoftKey');
        if ($act == 'add') {
            $data['AddTime']=time();//添加时添加时间与修改时间为同一时刻
            $data['EditTime']=time();
            if($softKey->insertData($data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加加密锁【'.$_POST['KeyNum'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else
            if ($act == 'edit') {
                $data['EditTime'] = time();
                $data['EditUserName'] = $this->getCookieUserName();
                $buffer = $softKey->selectData('KeyID,KeyNum','KeyID="'.$keyID.'"');
                if(!$buffer){
                    $this->setError('13607'); //加密锁不存在
                    exit;
                }
                if($softKey->updateData($data,'KeyID='.$keyID)===false){
                    $this->setError('30311'); //修改失败！
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'修改加密锁KeyID为【'.$keyID.'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除加密锁数据
     * @author demo
     */
    public function delete() {
        $keyID = $_POST['id']; //获取数据标识
        if(is_array($keyID)) $keyID=implode(',',$keyID);
        if (!$keyID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('SoftKey')->deleteData('KeyID IN ('.$keyID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除加密锁KeyID为【'.$keyID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}