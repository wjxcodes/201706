<?php
/**
 * 合作学校管理
 * @author  
 * @date 2015年5月4日
 */
namespace Manage\Controller;
class PartnerSchoolController extends BaseController {

    /**
     * 合作学校列表
     */
    public function index(){
        $pageName = '合作学校';
        $data     = '1=1';
        $map      = array();
        //简单查询
        if(!empty($_REQUEST['schoolSearch'])){
            $_REQUEST['schoolSearch'] = trim($_REQUEST['schoolSearch']);
            $map['schoolSearch']      = $_REQUEST['schoolSearch'];
            $data  .= ' and SchoolName like "' . $_REQUEST['schoolSearch'] . '%" ';
            $this->assign('searchName',$_REQUEST['schoolSearch']);//保持搜索词
        }
        $perPage    = C('WLN_PERPAGE');
           $partnerSchool=$this->getModel('PartnerSchool');
        $count      = $partnerSchool->selectCount($data,'SchoolID');
        $page       = page($count,$_GET['p'],$perPage).','.$perPage;
        $schoolList = $partnerSchool->pageData(
            '*',
            $data,
            'SchoolID DESC',//后台添加时间倒叙排列
            $page
        );
        if(!empty($schoolList)) {
            foreach ($schoolList as $i => $iSchoolList) {
                $schoolList[$i]['EndTime'] = date('Y-m-d H:i:s', $iSchoolList['EndTime']);
            }
        }
        $this->pageList($count, $perPage, $map);
        $this->assign('pageName',$pageName);
        $this->assign('schoolList',$schoolList);
        $this->display();
    }

    /**
     * 添加合作学校
     */
    public function add(){
        $pageName = '添加合作学校';
        $this->assign('pageName', $pageName);
        $this->display();
    }

    /**
     * 编辑合作学校
     */
    public function edit(){
        $schoolID = $_GET['id'];//获取数据标识
        if (empty ($schoolID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $where  = ' SchoolID='.$schoolID;
        $school = $this->getModel('PartnerSchool')->selectData(
            'SchoolName,IfRecom',
            $where,
            '',
            1
        );
        $this->assign('schoolID',$schoolID);
        $this->assign('school',$school[0]);
        $this->display('PartnerSchool/add');
    }


    /**
     * 删除合作学校
     */
    public function delete(){
        $schoolID = $_POST['id']; //获取数据标识
        if (!$schoolID) {
            $this->setError('30301',0,__URL__); //数据标识不能为空！
        }
        if($this->getModel('PartnerSchool')->deleteData(
              'SchoolID in ('.$schoolID.')'
            )===false){
               $this->setError('30302');//删除失败！
        }
        $this->adminLog($this->moduleName,'删除合作学校SchoolID为【'.$schoolID.'】的数据');
        $this->showSuccess('删除成功！', __URL__);
    }

    /**
     * 保存合作学校
     */
    public function save(){
         $data['SchoolName'] = trim($_POST['SchoolName']);
         $data['IfRecom']    = $_POST['IfRecom'];
         $data['EndTime']    = time();
         if($data['SchoolName']=='' || $data['IfRecom']===''){
             $this->setError('10124', 0);
               }
        $partnerSchool=$this->getModel('PartnerSchool');
         if($_POST['SchoolID']){//修改
             if (($result=$partnerSchool->updateData(
                      $data,
                     'SchoolID='.$_POST['SchoolID'])
                 ) === false) {
                 $this->setError('30311', 0); //修改失败！
             }
             $this->adminLog($this->moduleName,'修改合作学校SchoolID为【'.$_POST['SchoolID'].'】的数据');
             $this->showSuccess('修改成功！', U('PartnerSchool/edit?id='.$_POST['SchoolID']));
         }else{//添加
             //合作学校唯一性验证
             if($partnerSchool->selectData(
                     'SchoolID',
                     'SchoolName="'.$data['SchoolName'].'"',
                     '',
                     1
                 )!=false){
                 $this->setError('10125', 0); //学校已存在！
             }
             if (($result=$partnerSchool->insertData(
                     $data)
                 ) === false) {
                 $this->setError('30310',0); //添加失败！
             }
             $this->adminLog($this->moduleName,'添加合作学校【'.$data['SchoolName'].'】');
             $this->showSuccess('添加成功！', __URL__);
         }
    }
}
