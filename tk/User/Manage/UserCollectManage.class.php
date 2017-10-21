<?php
/**
 * @author demo
 * @date 2014年11月12日
 * @update 2015年1月21日
 */
/**
 * 收藏夹管理类，用于用户收藏夹的相关操作
 */
namespace User\Manage;
class UserCollectManage extends BaseController  {
    var $moduleName = '收藏夹管理';
    /**
     * 收藏夹列表浏览
     * @author demo
     */
    public function index() {
        $pageName = '收藏夹管理';
        $map = array();
        $data = ' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND SubjectID in ('.$this->mySubject.')';
        }
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName like "%' . $_REQUEST['name'] . '%" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if($_REQUEST['From']){
                $map['From']=$_REQUEST['From'];
                $data .= ' AND `From`='.$_REQUEST['From'];
            }
        }
        $perpage = C('WLN_PERPAGE');
        $count = $this->getModel('UserCollect')->selectCount(
            $data,
            '*');// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page=$page.','.$perpage;
        $list = $this->getModel('UserCollect')->pageData(
            '*',
            $data,
            'CollectID DESC',
            $page);
        if($list){
            $test = $this->getModel('TestReal');
            $subjectBuffer = SS('subject');
            $host = C('WLN_DOC_HOST');
            foreach($list as $i => $iList){
                //试题
                $buffer = $this->getModel('TestReal')->selectData(
                    '*',
                    'TestID='.$iList['TestID']);
                $list[$i]['ThisName'] = R('Common/TestLayer/strFormat',array($buffer[0]['Test']));
                if(!$list[$i]['ThisName']) $list[$i]['ThisName'] = '试题被移除';
                $list[$i]['SubjectName'] = $subjectBuffer[$list[$i]['SubjectID']]['SubjectName'];
            }
        }
        $this->pageList($count,$perpage,$map);
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 用户收藏夹删除
     * @author demo 
     */
    public function delete() {
        $id = $_POST['id']; //获取数据标识
        if($this->ifSubject && $this->mySubject){
            $subjectID = $this->getModel('UserCollect')->selectData(
                'SubjectID',
                'CollectID in ('.$id.')');
            $mySubjectArr = explode(',',$this->mySubject);
            foreach($subjectID as $i => $iSubjectID){
                if(!in_array($iSubjectID['SubjectID'],$mySubjectArr)){
                    $this->setError('30507');//您不能删除非所属学科收藏夹！
                }
            }
        }
        if (!$id) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if ($this->getModel('UserCollect')->deleteData(
                'CollectID in ('.$id.')') === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除搜藏信息CollectID为【'.$id.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}