<?php
/**
 * 核心系统常见问答CURD
 * @author demo
 * @date 2015-12-7
 */
namespace Index\Manage;
class CoreSystemFaqManage extends BaseController  {
    
    /**
     * 列表；
     * @author demo
     */
    public function index() {
        $pageName = '核心系统常见问答FAQ';
        //条件查询
        $map = array();
        $block = false;
        if($_REQUEST['name']){
            $map['name'] = $_REQUEST['name'];
            $where[] =' Question like "%'.$_REQUEST['name'].'%" ';
        }else{
            if(trim($_REQUEST['CSID']) != ''){
                $map['Question'] = $_REQUEST['CSID'];
                $where[] =' CSID = '.$_REQUEST['CSID'];
            }
            if($_REQUEST['Question']){
                $map['Question'] = $_REQUEST['Question'];
                $where[] =' Question like "%'.$_REQUEST['Question'].'%" ';
            }
            if($where){
                $block = true;
            }
        }
        $where = implode(' AND ', $where);
        $faqModel = $this->getModel('CoreSystemFaq');
        $perpage= C('WLN_PERPAGE');
        $count = $faqModel->getInfo($where,'count(FAQID) as c')[0]['c'];// 查询满足要求的总记录数
        $page=page($count,$_GET['p'],$perpage);
        $start = ($page-1)*$perpage;
        $page=$start.','.$perpage;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $faqModel->getInfo($where,'FAQID,Question,OrderID,CSID,User,LastTime,AddTime','OrderID DESC',$page);
        $csArr = $this->getModel('CoreSystem')->getInfo('','CSID,Title');//获得系统名 ID
        $newcsArr[0] = '（通用）';
        foreach ($csArr as $icsArr){
            $newcsArr[$icsArr['CSID']] = $icsArr['Title'];
        }
        unset($csArr);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('block',$block);//是否显示高级搜索
        $this->assign('csArr',$newcsArr);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加
     * @author demo
     */
    public function add() {
        $pageName = '添加核心系统FAQ';
        $act = 'add'; //模板标识
        $coreSys = $this->getModel('CoreSystem')->getInfo('','CSID,Title');
        /*载入模板标签*/
        $CSID = trim($_GET['CSID']);
        if($CSID != '') {
            $this->assign('edit',array('CSID'=>$CSID));
        }
        $this->assign('coreSys',$coreSys);
        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑
     * @author demo
     */
    public function edit() {
        $FAQID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($FAQID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑FAQ';
        $act = 'edit'; //模板标识
        $faqModel = $this->getModel('CoreSystemFaq');
        $edit = $faqModel->getInfo('FAQID='.$FAQID)[0];
        $edit['Answer'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $edit['Answer']);
        $edit['Answer'] = stripslashes_deep(formatString('IPReturn',$edit['Answer']));
        $coreSys = $this->getModel('CoreSystem')->getInfo('','CSID,Title');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit);
        $this->assign('coreSys',$coreSys);
        $this->assign('pageName', $pageName);
        $this->display('add');
    }
    /**
     * 保存
     * @author demo
     */
    public function save() {
        $FAQID = $_POST['FAQID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($FAQID) && $act == 'edit') {
            $this->setError('30301'); //数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223'); //模板标识不能为空！
        }
        $data = array ();
        $data['Question'] = trim($_POST['Question']);
        $data['Answer']=formatString('IPReplace',$_POST['Answer']);
        $data['CSID'] = $_POST['CSID'];
        $data['OrderID'] = (int)$_POST['OrderID'];
        $data['User'] = $this->getCookieUserName();//用户
        
        if(preg_match('/^[\x{4e00}-\x{9fa5}\d\w_\?？]{1,50}$/u',$data['Question']) < 1){
            R('Common/SystemLayer/showErrorMsg',array('问题名称存在非法字符', '', true, 3,'Public/error'));
        }
        if(empty($data['CSID']) && $data['CSID'] != 0){
            R('Common/SystemLayer/showErrorMsg',array('请选择归属系统', '', true, 3,'Public/error'));
        }
        if(empty($data['Answer'])){
            R('Common/SystemLayer/showErrorMsg',array('答案不能为空', '', true, 3,'Public/error'));
        }
        if(preg_match('/^\d{0,2}$/', $data['OrderID']) < 1 ){
            R('Common/SystemLayer/showErrorMsg',array('排序数最大99', '', true, 3,'Public/error'));
        }
        $faqModel = $this->getModel('CoreSystemFaq');
        if ($act == 'add') {
            //检查FAQ名称重复
            $isExist = $faqModel->getInfo('Question = "'.$data['Question'].'"','FAQID');
            if($isExist){
                R('Common/SystemLayer/showErrorMsg',array('问题已存在', '', true, 3,'Public/error'));//系统名称重复请更换！
                exit;
            }
            if($faqModel->save($data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog('CoreSystemFaq','添加核心系统FAQ【'.$_POST['Title'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } elseif ($act == 'edit') {
                $FAQID = $_POST['FAQID'];
                $isExist = $faqModel->getInfo('Question = "'.$data['Question'].'" AND FAQID !='.$FAQID,'FAQID');
                if($isExist){
                    R('Common/SystemLayer/showErrorMsg',array('问题与其他冲突，请更换', '', true, 3,'Public/error'));//系统名称重复请更换！
                    exit;
                }
                if($faqModel->save($data,$FAQID)===false){
                    $this->setError('30311'); //修改失败！
                }else{
                    //写入日志
                    $this->adminLog('CoreSystemFaq','修改核心系统FAQID为【'.$FAQID.'】的数据');
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
        if ($this->getModel('CoreSystemFaq')->delete('FAQID IN ('.$tagID.')')=== false) {
            $this->setError('30301'); //删除失败！
        } else {
            //写入日志
            $this->adminLog('CoreSystemFaq','删除常见问答FAQID为【'.$tagID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}