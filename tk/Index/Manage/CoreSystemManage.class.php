<?php
/**
 * 核心系统CURD
 * @author demo
 * @date 2015-12-7
 */
namespace Index\Manage;
class CoreSystemManage extends BaseController {
    
    /**
     * 列表；
     * @author demo
     */
    public function index() {
        $pageName = '核心系统';
        //条件查询
        $map = array();
        $block = false;//是否显示高级搜索
        if($_REQUEST['name']){
            $map['name'] = $_REQUEST['name'];
            $where[] =' Title like "%'.$_REQUEST['name'].'%" ';
        }else{
            if($_REQUEST['Title']){
                $map['Title'] = $_REQUEST['Title'];
                $where[] =' Title like "%'.$_REQUEST['Title'].'%" ';
            }
            if(trim($_REQUEST['Status']) != ''){//状态
                $map['Status'] = $_REQUEST['Status'];
                $where[] =' Status = "'.$_REQUEST['Status'].'" ';
            }
            if($where){
                $block = true;
            }
        }
        $where = implode(' AND ', $where);
        $csModel = $this->getModel('CoreSystem');
        $perpage= C('WLN_PERPAGE');
        $count = $csModel->getInfo($where,'count(CSID) as c')[0]['c'];// 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perpage);
        $start = ($page-1)*$perpage;
        $page=$start.','.$perpage;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $csModel->getInfo($where,'CSID,Title,ImgTitle,Status,Http,Admin,LastTime,AddTime,OrderID','OrderID DESC',$page);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('imgArr',$csModel->returnImgSrc());
        $this->assign('block',$block);//是否显示高级搜索
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加
     * @author demo
     */
    public function add() {
        $pageName = '添加核心系统';
        $act = 'add'; //模板标识
        /*载入模板标签*/
        $this->assign('imgArr',$this->getModel('CoreSystem')->returnImgSrc());
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑
     * @author demo
     */
    public function edit() {
        $CSID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($CSID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑系统';
        $act = 'edit'; //模板标识
        $csModel = $this->getModel('CoreSystem');
        $imgArr = $csModel->returnImgSrc();
        $edit = $csModel->getInfo('CSID='.$CSID)[0];
        //简介
        $edit['Description'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $edit['Description']);
        $edit['Description'] = stripslashes_deep(formatString('IPReturn',$edit['Description']));
        //系统简介
        $edit['Detail'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $edit['Detail']);
        $edit['Detail'] = stripslashes_deep(formatString('IPReturn',$edit['Detail']));
        //流程
        $edit['Flow'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $edit['Flow']);
        $edit['Flow'] = stripslashes_deep(formatString('IPReturn',$edit['Flow']));
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('imgArr',$imgArr);
        $this->assign('edit', $edit);
        $this->assign('pageName', $pageName);
        $this->display('add');
    }
    /**
     * 保存
     * @author demo
     */
    public function save() {
        $CSID = $_POST['CSID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($CSID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $data = array ();
        $data['Title'] = trim($_POST['Title']);
        $data['Description']=formatString('IPReplace',$_POST['Description']);
        $data['Detail']=formatString('IPReplace',$_POST['Detail']);
        $data['Flow']=formatString('IPReplace',$_POST['Flow']);
        $data['ImgTitle'] = $_POST['ImgTitle'];
        $data['Http'] = $_POST['Http'];
        $data['Status'] = $_POST['Status'];
        $data['OrderID'] = (int)$_POST['OrderID'];
        $data['Admin'] = $this->getCookieUserName();
        
        if(preg_match('/^[\x{4e00}-\x{9fa5}\d\w_]{1,50}$/u',$data['Title']) < 1){
            R('Common/SystemLayer/showErrorMsg',array('标题名称存在非法字符', '', true, 3,'Public/error'));
        }
        if(preg_match('/^\d{0,2}$/', $data['OrderID']) < 1 ){
            R('Common/SystemLayer/showErrorMsg',array('排序数最大99', '', true, 3,'Public/error'));
        }
        
        if(empty($data['Description'])){
            R('Common/SystemLayer/showErrorMsg',array('系统描述不能为空', '', true, 3,'Public/error'));
        }
//         if(preg_match('/[\w\d\-\/_]*/i',$data['Http']) < 1){
//             R('Common/SystemLayer/showErrorMsg',array('如果输入任务URL请输入http://开头的正确地址', '', true, 3,'Public/error'));
//         }
        $csModel = $this->getModel('CoreSystem');
        if ($act == 'add') {
            //检查系统名称重复
            $isExist = $csModel->getInfo('Title = "'.$data['Title'].'"');
            if($isExist){
                R('Common/SystemLayer/showErrorMsg',array('系统名称重复，请更换', '', true, 3,'Public/error'));//系统名称重复请更换！
                exit;
            }
            if($csModel->save($data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog('CoreSystem','添加核心系统【'.$_POST['Title'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } elseif ($act == 'edit') {
                $CSID = $_POST['CSID'];
                $isExist = $csModel->getInfo('Title = "'.$data['Title'].'" AND CSID !='.$CSID);
                if($isExist){
                    R('Common/SystemLayer/showErrorMsg',array('系统名称与其他冲突，请更换', '', true, 3,'Public/error'));//系统名称重复请更换！
                    exit;
                }
                if($csModel->save($data,$CSID)===false){
                    $this->setError('30311'); //修改失败！
                }else{
                    //写入日志
                    $this->adminLog('CoreSystem','修改核心系统CSID为【'.$CSID.'】的数据');
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
        $faqModel = $this->getModel('CoreSystemFaq');
        $faqArray = $faqModel->getInfo('CSID IN ('.$tagID.')');
        if ($faqModel->delete('CSID IN ('.$tagID.')')=== false) {//删除关联FAQ
            $this->setError('30301'); //删除失败！
        } else {
            //写入日志
            $this->adminLog('CoreSystem,CoreSystemFaq','删除系统FAQID为【'.$tagID.'】的数据');
        }
        if ($this->getModel('CoreSystem')->delete('CSID IN ('.$tagID.')')=== false) {
            $faqModel->allSave($faqArray);//重新添加删除的FAQ
            $this->setError('30301'); //删除失败！
        } else {
            //写入日志
            $this->adminLog('CoreSystem','删除系统CSID为【'.$tagID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}