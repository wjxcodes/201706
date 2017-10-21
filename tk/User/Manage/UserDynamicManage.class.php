<?php
/**
 * @author demo
 * @date 2015-10-22
 */

/**
 * 题型配置类，用于题型的相关操作
 */
namespace User\Manage;
class UserDynamicManage extends BaseController  {
    var $moduleName = '用户资源管理';
    var $from=array('case'=>'导学案',
                    'work'=>'作业',
                    'doc'=>'试卷'
    );
    /**
     * 浏览题型列表
     * @author demo
     */
    public function index(){
        $pageName="用户资源管理列表";
        $map=array();
        $data = ' 1 = 1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND Title like "%'.$_REQUEST['name'].'%" ';
        }else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] .' "';
            }
            if ($_REQUEST['TempName']) {
                $map['TempName'] = $_REQUEST['TempName'];
                $data .= ' AND Title = "' . $_REQUEST['TempName'] .' "';
            }
            if (is_numeric($_REQUEST['SubjectID']) && $_REQUEST['SubjectID']!=0) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID ="' . $_REQUEST['SubjectID'] . '" ';
            }
            if ($_REQUEST['docFrom']) {
                $map['docFrom'] = $_REQUEST['docFrom'];
                $data .= ' AND Classification ="' . $_REQUEST['docFrom'] . '" ';
            }
        }
        $perPage = C('WLN_PERPAGE');
        $count = $this->getModel('UserDynamic')->selectCount(
            $data,
            "*"); // 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perPage).','.$perPage;
        $list = $this->getModel('UserDynamic')->pageData(
            '*',
            $data,
            'UDID desc',
            $page
        );
        $this->pageList($count,$perPage,$map);//载入分页
        $subjectArray=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subjectArray[$iList['SubjectID']]['ParentName'].$subjectArray[$iList['SubjectID']]['SubjectName'];
            $list[$i]['docFrom']=$this->from[$list[$i]['Classification']];
        }
        $subjectList = SS('subjectParentId');; //获取学科数据集
        $this->assign('pageName',$pageName);
        $this->assign('subjectArray',$subjectList);
        $this->assign('docFrom',$this->from);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 审核通过文档状态
     * @author demo
     */
    public function check(){

        if(!empty($_REQUEST['id'])){
            //文档权限
            $buffer=$this->getModel('UserDynamic')->selectData(
                '*',
                'UDID in ('.$_REQUEST['id'].')'
            );
            if($this->ifSubject && $this->mySubject){
                if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712','',__URL__); //您不能编辑非所属学科文档！
                }
            }
            $UDID=$_REQUEST['id'];
            $checkStatus=$_REQUEST['checkStatus'];
            if(!empty($checkStatus)){
                //批量设置默认审核通过
                $checkStatus=1;
            }
            $data['CheckStatus']=$checkStatus;
            $result=$this->getModel('UserDynamic')->updateData($data,'UDID in ('.$UDID.')');
            if($result!=false){
                $this->showSuccess('审核修改成功！', __URL__);
            }
        }
    }


}