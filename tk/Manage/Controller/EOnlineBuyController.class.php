<?php
/**
 * @author demo
 * @date 2017年3月21日
 */
 /**
  *在线联考用户控制器类，用于处理在线联考用户档案操作
  */
namespace Manage\Controller;
class EOnlineBuyController extends BaseController{
    private $moduleName = '在线联考用户';
    /**
     * 浏览报名用户
     * @author demo
     */
    public function index() {
        $pageName = '报名用户管理';

        //获取检错条件
        $where=$this->getWhere($_REQUEST);

        $count = $this->getModel('ExamBuy')->selectCount(
                    $where['data'],
                    '*');

        $perpage = C('WLN_PERPAGE');
        $page = page($count,$_GET['p'],$perpage);
        $page =$page.','.$perpage;

        $list=$this->getModel('ExamBuy')->pageData(
                    '*',
                    $where['data'],
                    'BuyID Desc',
                    $page
                );
            //获取用户组数据
        $paperTypeArray=array();//考试类型id
        $paperTypeBuffer=array();//考试类型数据集
        $userIDArray=array();//用户id
        $userIDBuffer=array();//用户数据集
        if($list){
            foreach($list as $i=>$iList){
                $userIDArray[]=$iList['UserID'];
                $paperTypeArray[]=$iList['PaperType'];
            }
        }

        //获取用户数据
        if($userIDArray){
            $buffer=$this->getModel('User')->selectData('UserID,UserName','UserID in ('.implode(',',$userIDArray).')');
            foreach($buffer as $iBuffer){
                $userIDBuffer[$iBuffer['UserID']]=$iBuffer['UserName'];
            }
        }
        //获取考试类型
        if($paperTypeArray){
            $buffer=$this->getModel('ExamType')->selectData('TypeID,TypeName','TypeID in ('.implode(',',$paperTypeArray).')');
            foreach($buffer as $iBuffer){
                $paperTypeBuffer[$iBuffer['TypeID']]=$iBuffer['TypeName'];
            }
        }

        foreach($list as $i=>$iList){
            $list[$i]['UserName']=$userIDBuffer[$iList['UserID']];
            $list[$i]['TypeName']=$paperTypeBuffer[$iList['PaperType']];
            if($list[$i]['CheckTime']) $list[$i]['CheckTime']=date('Y-m-d H:i:s',$iList['CheckTime']);
            else $list[$i]['CheckTime']='未验证';
            if($list[$i]['AddTime']) $list[$i]['AddTime']=date('Y-m-d H:i:s',$iList['AddTime']);
            else $list[$i]['AddTime']='无';
            $list[$i]['IfWL']=$list[$i]['IfWL']==1?'文科':'理科';
            if($list[$i]['IfWL']==0) $list[$i]['IfWL']='通用';
        }

        $this->pageList($count, $perpage, $where['map']);

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加用户
     * @author demo
     */
    public function add() {
        $pageName = '添加报名用户';
        $act = 'add'; //模板标识

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑用户
     * @author demo
     */
    public function edit() {
        $userID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($userID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑报名用户';
        $act = 'edit'; //模板标识

        $edit = $this->getModel('ExamBuy')->selectData(
            '*',
            'BuyID=' . $userID,
            '',
            1);
        if($edit[0]){
            $edit[0]['AddTime']=date('Y-m-d H:i:s',$edit[0]['AddTime']);
            if($edit[0]['CheckTime']) $edit[0]['CheckTime']=date('Y-m-d H:i:s',$edit[0]['CheckTime']);
            else $edit[0]['CheckTime']=0;
            if($edit[0]['UserID']){
                $buffer=$this->getModel('User')->findData('UserID,UserName','UserID='.$edit[0]['UserID']);
                $edit[0]['UserName']=$buffer['UserName'];
            }
        }

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('EOnlineBuy/add');
    }
    /**
     * 保存用户
     * @author demo
     */
    public function save() {
        $buyID = $_POST['BuyID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($buyID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $exambuy = $this->getModel('ExamBuy');
        //获取字段
        $data = array ();
        $data['PaperType'] = $_POST['PaperType'];
        $data['Phonecode'] = $_POST['Phonecode'];
        $data['IfWL'] = $_POST['IfWL'];
        $data['Status'] = $_POST['Status'];
        if ($act == 'add') {
            //检查手机重复
            $buffer = $exambuy->findData('BuyID','Phonecode="'.$data['Phonecode'].'" and PaperType="'.$data['PaperType'].'"');
            if($buffer){
                $this->setError('该手机号已经存在于本次考试购买记录。');
            }
            $data['AddTime'] = time();

            $lastID = $exambuy->insertData(
                $data);
            if ($lastID === false) {
                $this->setError('30310'); //添加失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '添加用户【' . $_POST['Phonecode'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else if ($act == 'edit') {
            //检查手机重复
            $buffer = $exambuy->findData('BuyID','Phonecode="'.$data['Phonecode'].'" and PaperType="'.$data['PaperType'].'" and BuyID!="'.$buyID.'"');
            if($buffer){
                $this->setError('该手机号已经存在于本次考试购买记录。');
            }
                if ($exambuy->updateData($data,'BuyID='.$buyID) === false) {
                     $this->setError('30311'); //修改失败！
                } else {
                    //写入日志
                    $this->adminLog($this->moduleName, '修改用户buyID为【' . $_POST['BuyID'] . '】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }

    /**
     * 锁定用户
     * @author demo
     */
    public function replace() {
        $data = array ();
        $status = $_POST['status'];
        $buyID = $_POST['wid']; //获取数据标识
        if($status){
            $status=0;
        }else{
            $status=1;
        }
        $data['Status'] = $status;
        if (!$buyID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('ExamBuy')->updateData(
                $data,
                'BuyID in (' . $buyID . ')') === false) {
            $this->setError('30824'); //更改状态失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '修改用户BuyID为【' . $buyID . '】的状态【' . ($data['Status'] == 1 ? '锁定' : '正常') . '】');
            $this->setBack('更改状态成功！');
        }
    }
    /**
     * 删除用户
     * @author demo
     */
    public function delete() {
        $buyID = $_POST['id']; //获取数据标识
        if (!$buyID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('ExamBuy')->deleteData(
                'BuyID in ('.$buyID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除用户BuyID为【' . $buyID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }

    /**
     * 批量上传用户
     * @author demo
     */
    public function uploads(){
        $pageName='批量上传用户';
        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName',$pageName);
        $this->display();
    }

    /**
     * 批量上传用户插件
     * @author demo
     */
    public function uploadify(){
        if (!empty($_FILES)) {
            $path=R('Common/UploadLayer/uploadExcel',array('user','excel_stu'));
            if(is_array($path)){
                exit($path[1]);
            }
            $brr=unserialize($path);
        }
        if(empty($brr)){
            $data['description']='未提取到报名用户信息，请检查上传文档！!';
            $data['msg']=$brr;
            $this->getModel('Base')->addErrorLog($data);
            exit('未提取到用户信息，请检查上传文档！');
        }
        $output=array();
        $output=$this->createUser($brr);
        if(!is_array($output)) exit($output);
        exit('success');
    }

    /**
     * 批量上传用户上传文档处理
     * @param $arr array 从上传文档中获取到的数据
     * @author demo
     */
    protected function createUser($arr){
        $examBuy = $this->getModel('ExamBuy');
        $resultArr = array();
        $uploadInfo=array();//上传文档信息
        foreach($arr as $i=>$iArr){
            $num=0;
            foreach($iArr as $j=>$jArr){
                $iArr[$num] = str_replace(array("/r/n", "/r", "/n",' '), '', $jArr);
                $num++;
            }
            $resultArr[$i]=$iArr;
            $resultArr[$i]['order']=$i+1;
            if(empty($iArr[0])){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '手机号为空';
                continue;
            }
            $iArr[1]=trim($iArr[1]);
            if($iArr[1]!='文科' && $iArr[1]!='理科'){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '请填写文理科！';
                continue;
            }
            //标记上传文档重复数据
            if(in_array($iArr[0],$uploadInfo)){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = '手机号重复';
                continue;
            }else{
                $uploadInfo[]=$iArr[0];
            }
            //验证用户名，电话，邮箱是否重复
            $checkInfo = $this->checkPhone($iArr[0]);
            if($checkInfo[0]==0){
                $resultArr[$i]['error'] = 1;
                $resultArr[$i]['message'] = $checkInfo[1];
            }
        }
        $resultArr['serial'] = base64_encode(serialize($resultArr));
        $resultArr['return'] = 2;
        $this->setBack($resultArr);
    }

    /**
     * 验证手机号是否重复
     * @author demo
     */
    public function checkPhone($phonecode,$paperType){
        $examBuy = $this->getModel('ExamBuy');
        //检查手机重复
        $buffer = $exambuy->findData('BuyID','Phonecode="'.$phonecode.'" and PaperType="'.$paperType.'"');
        if($buffer){
            return [0,'手机号重复'];
        }
    }

    /**
     * 批量上传用户保存
     * @author demo
     */
    public function addUsers(){
        $delOrder = $_POST['delOrder'];
        $status = $_POST['Status'];
        $paperType = $_POST['PaperType'];
        $userInfo = unserialize(base64_decode($_POST['serialData']));
        $data = array();
        $delArray = array();
        $delArray=explode(',',$delOrder);
        foreach($userInfo as $i=>$iUserInfo){
            if(empty($iUserInfo) || in_array($i+1,$delArray)){
                continue;
            }
            $data['PaperType'] = $paperType;
            $data['Phonecode'] = trim($iUserInfo[0]);
            $data['IfWL'] = trim($iUserInfo[1])=='文科'? 1:2;
            $data['Status'] = $status;
            $data['AddTime'] =time();
            $userID = $this->getModel('ExamBuy')->insertData(
                $data);
            if($userID===false){
                $this->setError('30310',1);
            }
        }
        $this->setBack('success');
    }

    /**
     * 批量上传用户excel模板下载
     * @author demo
     */
    public function excelUser() {
        header('Location:'.C('WLN_HTTP').'/examBuy.xls');
    }

    /**
     * 获取查询条件
     * @param $where array
     * @return array
     * @author demo
     */
    private function getWhere($where){
        //获取检错条件
        $map = array ();
        $data = ' 1=1 ';

        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND Phonecode = "' . $_REQUEST['name'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['UserID']) {
                $map['UserID'] = $_REQUEST['UserID'];
                $data .= ' AND UserID = "' . $_REQUEST['UserID'] . '" ';
            }
            if ($_REQUEST['Phonecode']) {
                $map['Phonecode'] = $_REQUEST['Phonecode'];
                $data .= ' AND Phonecode = "' . $_REQUEST['Phonecode'] . '" ';
            }
            if (is_numeric($_REQUEST['PaperType'])) {
                $map['PaperType'] = $_REQUEST['PaperType'];
                $data .= ' AND PaperType = "' . $_REQUEST['PaperType'] . '" ';
            }
            if (is_numeric($_REQUEST['IfWL'])) {
                $map['IfWL'] = $_REQUEST['IfWL'];
                $data .= ' AND IfWL ="' . $_REQUEST['IfWL'] . '" ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status = "' . $_REQUEST['Status'] . '" ';
            }
            if (is_numeric($_REQUEST['CheckStatus'])) {
                $map['CheckStatus'] = $_REQUEST['CheckStatus'];
                if($_REQUEST['CheckStatus']==1) $data .= ' AND CheckTime =0';
                if($_REQUEST['CheckStatus']==2) $data .= ' AND CheckTime >0';
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }
}