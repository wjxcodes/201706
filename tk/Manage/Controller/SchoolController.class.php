<?php
/**
 * @author demo
 * @date 2014年12月27日
 */
/**
 * 学校控制器类，用于处理学校相关操作
 */
namespace Manage\Controller;
class SchoolController extends BaseController  {
    var $moduleName='学校配置';
    /**
     * 浏览学校列表
     * @author demo
     */
    public function index() {
        $pageName = '学校管理';
        $data = ' 1=1 ';
        $map=array(); //分页链接参数
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND SchoolName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['SchoolName']) {
                $map['SchoolName'] = $_REQUEST['SchoolName'];
                $data .= ' AND SchoolName like "%' . $_REQUEST['SchoolName'] . '%" ';
            }
            if ($_REQUEST['SchoolID']) {
                if(is_numeric($_REQUEST['SchoolID'])){
                    $map['SchoolID'] = $_REQUEST['SchoolID'];
                    $data .= ' AND SchoolID = ' . $_REQUEST['SchoolID'] . ' ';
                }else{
                    $this->setError('30502');
                }
            }
            $areaID=isset($_REQUEST['AreaID']) ? $_REQUEST['AreaID'] : '';
            if(is_array($areaID)){
                $countArea=count($areaID);
                $tmpID=$areaID[$countArea-1];
                if(empty($tmpID) && $countArea>1) $tmpID=$areaID[$countArea-2];
                $areaID=$tmpID;
            }
            if (!empty($areaID)) {
                $map['AreaID'] = $areaID;
                $areaIDList=SS('areaIDList');
                if($areaIDList[$areaID]) $areaID=$areaIDList[$areaID];
                $data .= ' AND AreaID in (' . $areaID . ') ';
            }
            if ($_REQUEST['Type']) {
                $map['Type'] = $_REQUEST['Type'];
                $data .= ' AND Type=' .$_REQUEST['Type'];
            }
            $status=isset($_REQUEST['Status']) ? $_REQUEST['Status'] : '';
            if (is_numeric($status)) {
                $map['Status'] = $status;
                $data .= ' AND Status = ' . $status . ' ';
            }
        }
        $perpage = C('WLN_PERPAGE');
           $school=$this->getModel('School');
        $count = $school->selectCount(
            $data,
            "SchoolID"); // 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perpage);
        $page=$page .','.$perpage;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $school->pageData(
            '*',
            $data,
            'SchoolID desc',
            $page);
        $this->pageList($count, $perpage, $map);

        $buffer=SS('areaParentPath');  // 缓存父类list数据
        $bufferz=SS('areaChildList');  // 缓存子类list数据
        $buffera=SS('areaList');  // 缓存list数据
        $areaArray = $bufferz[0]; //省份数据集
        //为省份数据添加路径
        if($list){
            foreach($list as $i=>$iList){
                $bufferx=array();
                if($buffer[$iList['AreaID']]){
                    foreach($buffer[$iList['AreaID']] as $j=>$jBuffer){
                        $bufferx[]=$jBuffer['AreaName'];
                    }
                }
                $bufferx[]=$buffera[$iList['AreaID']]['AreaName'];
                $list[$i]['AreaPath']=implode(' >> ',$bufferx);
            }
        }
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('areaArray', $areaArray); //省份数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加学校
     * @author demo
     */
    public function add() {
        $pageName = '添加学校';
        $act = 'add'; //模板标识
        $buffer=SS('areaChildList');  // 缓存子类list数据
        $areaArray = $buffer[0]; //省份数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('areaArray', $areaArray); //省份数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑学校
     * @author demo
     */
    public function edit() {
        $schoolID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($schoolID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑学校';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('School')->selectData(
            '*',
            'SchoolID='.$schoolID);//当前数据集

        //查找父类id
        $areaParent=$this->getModel('Area')->getAreaStr($edit[0]['AreaID']);
        $bufferx=SS('areaChildList');  // 缓存子类list数据
        $areaArray=$bufferx[0];//获取省份
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('areaArray', $areaArray); //地区数据集
        $this->assign('areaParent', $areaParent); //父类数据
        $this->assign('pageName', $pageName);
        $this->display('School/add');
    }
    /**
     * 保存学校
     * @author demo
     */
    public function save() {
        $schoolID=$_POST['SchoolID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if(empty($schoolID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); // 模板标识不能为空！
        }
        $data=array();
        $data['SchoolName']=$_POST['SchoolName'];
        $data['Type']=$_POST['Type'];
        $data['Status']=$_POST['Status'];
        $data['OrderID']=$_POST['OrderID'];
        $areaID=$_POST['AreaID'];
        if(is_array($areaID)) $areaID=$areaID[count($areaID)-1];
        if(empty($areaID)){
            $this->setError('30727'); //请选择最后一级地区数据！
        }
        $data['AreaID']=$areaID;
        $school=$this->getModel('School');
        if($act=='add'){
            if($school->insertData(
                $data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加学校【'.$_POST['SchoolName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        }else if($act=='edit'){
            $data['SchoolID']=$schoolID;
            if($school->updateData(
                    $data,
                    'SchoolID='.$schoolID)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改SchoolID为【'.$schoolID.'】的数据');
                $this->showSuccess('修改成功！',__URL__);
            }
        }
    }
    /**
     * 删除学校
     * @author demo
     */
    public function delete() {
        $schoolID=$_POST['id'];    //获取数据标识
        if(!$schoolID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->getModel('School')->deleteData(
            'SchoolID in ('.$schoolID.')')===false){
            $this->setError('30310'); //删除失败！
        }else{
            //写入日志
            $this->adminLog($this->moduleName,'删除SchoolID为【'.$schoolID.'】的数据');
            $this->showSuccess('删除成功！',__URL__);
        }
    }
}