<?php
/**
 * @author demo
 * @date 2016年1月31日
 */
 /**
  *活动专题管理
  */
namespace Manage\Controller;
class ActivitySpecialController extends BaseController  {
    private $moduleName = '活动专题管理';
    /**
     * 浏览活动专题；
     * @author demo
     */
    public function index(){
        $pageName = '活动专题管理';
        $where='1=1';
        $map=array();
        if($_REQUEST['name']){
            $where.=' AND ActivityName like "%'.$_REQUEST['name'].'%"';
            $map['ActivityName']=$_REQUEST['name'];
        }
        $activityModel = $this->getModel('ActivitySpecial');
        $count = $activityModel->selectCount(
            $where,
            'ActivityID');
        $perpage = C('WLN_PERPAGE');
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage). ',' . $perpage;
        $activityData = $activityModel->pageData(
            '*',
            $where,
            'AddTime DESC',
            $page);
        $this->pageList($count, $perpage, $map);
        foreach($activityData as $i=>$iActivityData){
            $activityData[$i]['StartTime'] = date('Y-m-d H:i:s',$iActivityData['StartTime']);
            $activityData[$i]['EndTime'] = date('Y-m-d H:i:s',$iActivityData['EndTime']);
            $activityData[$i]['AddTime'] = date('Y-m-d H:i:s',$iActivityData['AddTime']);
            $activityData[$i]['ImgUrl'] = C('WLN_DOC_HOST').$iActivityData['ImgUrl'];
        }
        /*载入模板标签*/
        $this->assign('activityData', $activityData); //能力 属性数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加活动专题
     * @author demo
     */
    public function add(){
        $pageName = '添加活动专题';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑活动专题；
     * @author demo
     */
    public function edit(){
        $activityID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($activityID)){
            $this->setError('30301'); //数据标识不能为空
        }
        $pageName = '编辑活动专题';
        $act = 'edit'; //模板标识
        $edit = $this->getModel('ActivitySpecial')->selectData(
            '*',
            'ActivityID='.$activityID
            );
        $edit[0]['StartTime'] = date('Y-m-d',$edit[0]['StartTime']);
        $edit[0]['EndTime'] = date('Y-m-d',$edit[0]['EndTime']);
        $edit[0]['ImgUrl'] = C('WLN_DOC_HOST').$edit[0]['ImgUrl'];
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('pageName', $pageName);
        $this->display('ActivitySpecial/add');
    }
    /**
     * 保存活动专题；
     * @author demo
     */
    public function save(){
        //接收参数
        $activityID=$_POST['ActivityID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        $activityName = $_POST['ActivityName']; //活动名称
        $activityWebsite = $_POST['ActivityWebsite'];//活动地址
        $startTime = $_POST['StartTime'];//开始时间
        $endTime = $_POST['EndTime'];//结束时间
        //判断数据标识
        if(empty($activityID) && $act=='edit'){
             $this->setError('30301'); //'数据标识不能为空！
        }
        if(empty($activityName)){
            $this->setError('17201'); //活动名称不能为空
        }
        if(empty($activityWebsite)){
            $this->setError('17202'); //活动地址不能为空
        }
        if(empty($startTime)){
            $this->setError('17203'); //活动开始时间不能为空
        }
        if(empty($endTime)){
            $this->setError('17204'); //活动结束时间不能为空
        }
        $data=array();
        if ($_FILES['imgName']['size']) {
            $path = R('Common/UploadLayer/uploadImg');
            if(strstr($path,'error')){
                $this->setError('30725',0,'',$path);
            }
            $data['ImgUrl']=$path;
        }else if($act=='add'){
            $this->setError('17205');//请选择活动图片
        }

        $data['ActivityName'] = $activityName;
        $data['ActivityWebsite'] = $activityWebsite;

        if(checkString('isDate',$startTime)){
            $data['StartTime'] = strtotime($startTime);
        }else{
            $this->setError('17206');//请填写正确的开始时间
        }
        if(checkString('isDate',$endTime)){
            $data['EndTime'] = strtotime($endTime);
        }else{
            $this->setError('17207');//请填写正确的结束时间
        }
        $data['AddName'] = $this->getCookieUserName();
           $activityModel=$this->getModel('ActivitySpecial');
        if($act=='add'){
            $data['AddTime'] = time();
            if($activityModel->insertData($data)===false){
                $this->setError('30310'); //添加活动专题失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加活动【'.$activityName.'】');
                $this->showSuccess('添加活动专题成功！',__URL__);
            }
        }else if($act=='edit'){
            if($activityModel->updateData($data,'ActivityID='.$activityID)===false){
                $this->setError('30311'); //修改能力 属性失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改ActivityID为【'.$activityID.'】的数据');
                $this->showSuccess('修改活动专题成功！',__URL__);
            }
        }
    }
}
