<?php
/**
 * 用户收藏目录类，用户管理用户收藏目录
 * @author demo
 * @date 2014年10月14日
 * @update 2015年1月21日
 */
namespace User\Manage;
class UserCatalogManage extends BaseController  {
    var $moduleName = '收藏夹管理';
    /**
     * 浏览目录列表
     * @author demo
     * @date 2014年10月14日
     */
    public function index() {
        $pageName = '收藏目录管理';
        $userCatalog = $this->getModel('UserCatalog');
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
        }
        $perpage = C('WLN_PERPAGE');
        $count = $userCatalog->selectCount(
            $data,
            'CatalogID'
            ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage).','.$perpage;
        $list = $userCatalog->pageData(
            '*',
            $data,
            'CatalogID DESC',
            $page);
        if($list){
            $subjectBuffer = SS('subject');
            $host = C('WLN_DOC_HOST');
            foreach($list as $i => $listn){
                $list[$i]['SubjectName']=$subjectBuffer[$list[$i]['SubjectID']]['ParentName'].$subjectBuffer[$list[$i]['SubjectID']]['SubjectName'];
            }
        }
        $this->pageList($count,$perpage,$map);
        
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 删除目录及目录下的子目录和试题
     * @author demo
     * @date  2014年10月14日
     */
    public function delete() {
        $catalogID = $_POST['id']; //获取数据标识
        if (!$catalogID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $userCatalog = $this->getModel('UserCatalog');
        if($this->ifSubject && $this->mySubject){
            $catalogData = $userCatalog->getCatalogList('SubjectID','CatalogID in('.$$catalogID.')');
            $subjectID = explode(',',$this->mySubject);
            foreach($catalogData as $i => $iCatalogData){
                if(!in_array($iCatalogData['SubjectID'],$subjectID)){
                    $this->setError('30507');//您不能删除非所属学科收藏目录！
                }
            }
        }
        //删除该目录下的子目录包括自身
        $where = 'FatherID='.$catalogID;
        $field = 'CatalogID';
        $subCata = $userCatalog->getCatalogList($field,$where);
        $cataid = array();
        $cataid[0] = $catalogID;
        $i = 1;
        if($subCata){
            foreach($subCata as $sub){
                $cataid[$i] = $sub['CatalogID'];
                $i++;
            }
        }
        $catalogID = implode(',',$cataid);
        $userCollect = $this->getModel('UserCollect');
        $data['CatalogID'] = 0;
        $catade = $userCollect->updateData(
            $data,
            'CatalogID in('.$catalogID.')');//用于将该目录下的试题移动到未分类
        $buffer = $userCatalog->deleteData(
            'CatalogID in('.$catalogID.')');//用于删除该目录下的子目录，包括其本身
        if ($buffer === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除搜藏目录CollectID为【'.$id.'】及该目录内的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}