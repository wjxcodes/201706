<?php
/**
 * @author demo
 * @date 2014-11-12
 * @update 2015年1月26日
 */
/**
 * 自定义打分管理类，用于自定义打分的相关操作
 */
namespace Test\Manage;
class TestMarkManage extends BaseController  {
    var $moduleName = '自定义打分管理';
    /**
     * 自定义试题打分列表浏览
     * @author demo 
     */
    public function index() {
        $pageName='自定义打分';
        $map=array();
        $data=' 1=1 ';
        if($this->ifSubject && $this->mySubject){
            $data .= ' AND a.SubjectID in ('.$this->mySubject.')';
        }
        if($_REQUEST['name']){
            //简单查询
            $map['name']=$_REQUEST['name'];
            $data.=' AND a.MarkName like "%'.$_REQUEST['name'].'%" ';
        }else{
            //高级查询
            if($_REQUEST['MarkName']){
                $map['MarkName']=$_REQUEST['MarkName'];
                $data.=' AND a.MarkName like "%'.$_REQUEST['MarkName'].'%" ';
            }
            if($_REQUEST['SubjectID']){
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能搜索非所属学科自定义打分！
                    }
                }
                $map['SubjectID']=$_REQUEST['SubjectID'];
                $data.=' AND a.SubjectID = "'.$_REQUEST['SubjectID'].'" ';
            }
        }
        $perpage = C('WLN_PERPAGE');
        $count = $this->getModel('TestMark')->selectCount(
            'MarkID',
            $data
        );
        //进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page = $page.','.$perpage;
        $baseObj = D('Base');
        $list = $baseObj->unionSelect('testMarkPageData',$data, $page);
        $subject=SS('subject');
        foreach($list as $i=>$iList){
            $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];
        }
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName);
        $this->display();
    }
    /**
     * 添加自定义试题打分
     * @author demo
     */
    public function add() {
        $pageName = '添加自定义打分';
        $act = 'add'; //数据标识
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        /*载入模板标签*/
        $this->assign('act', $act);
        $this->assign('subjectArray', $subjectArray); // 赋值数据集
        $this->assign('pageName', $pageName);
        $this->display();
    }
    /**
     * 编辑自定义试题打分
     * @author demo 
     */
    public function edit() {
        $pageName = '修改自定义打分';
        $act = 'edit'; //数据标识
        $id = $_GET['id'];
        //判断数据标识
        if (empty($id)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $edit = $this->getModel('TestMark')->selectData(
            '*',
            'MarkID='.$id
        );
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712');//您不能编辑非所属学科自定义打分！
            }
        }
        $edit[0]['MarkListx'] = formatString('str2Arr',$edit[0]['MarkList']);
        $subjectArray = SS('subjectParentId'); //获取学科数据集
        /*载入模板标签*/
        $this->assign('edit', $edit[0]);
        $this->assign('act', $act);
        $this->assign('subjectArray', $subjectArray); // 赋值数据集
        $this->assign('pageName', $pageName);
        $this->display('TestMark/add');
    }
    /**
     * 保存自定义试题打分
     * @author demo 
     */
    public function save() {
        $markID = $_POST['MarkID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($markID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }
        $data = array ();
        $data['MarkName'] = formatString('changeStr2Html',$_POST['MarkName']);
        $data['SubjectID'] = $_POST['SubjectID'];
        $data['Style'] = $_POST['Style'];
        $data['OrderID'] = $_POST['OrderID'];
        
        //打分规则列表
        $mark = $_POST['Mark'];
        $list = $_POST['List'];
        
        //判断是否有重复值
        if(count($mark) != count(array_unique($mark))){
            $this->setError('1X2004');//规则分值不能重复！
        }
        $listMark = array();
        if(!is_array($mark)) $mark = array($mark);
        if(!is_array($list)) $list = array($list);
        foreach($mark as $i => $iMark){
            if(empty($iMark) || empty($list[$i])) continue;
            $listMark[]=$iMark.'|'.$list[$i];
        }
        if(empty($listMark)) $this->setError('1X2005');//打分规则不能为空
        $data['MarkList'] = '#'.implode('#',$listMark).'#';
        $testMarkObj = $this->getModel('TestMark');
        if ($act == 'add') {
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712');//您不能添加非所属学科自定义打分！
                }
            }
            if ($testMarkObj->insertData(
                    $data) === false) {
                $this->setError('30310');//添加失败！
            } else {
                //写入日志
                $this->adminLog($this->moduleName, '添加打分规则【' . $_POST['MarkName'] . '】');
                $this->showSuccess('添加成功！', __URL__);
            }
        } else
            if ($act == 'edit') {
                $subject = $testMarkObj->selectData(
                    'SubjectID',
                    'MarkID='.$markID);
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能编辑非所属学科自定义打分！
                    }
                    if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30712');//您不能编辑为非所属学科自定义打分！
                    }
                }
                if ($testMarkObj->updateData(
                        $data,
                        'MarkID='.$markID) === false) {
                    $this->setError('30311');//修改失败！
                } else {
                    //写入日志
                    $this->adminLog($this->moduleName, '修改打分规则MarkID为【' . $_POST['MarkID'] . '】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除自定义试题打分
     * @author demo 
     */
    public function delete() {
        $markID = $_POST['id']; //获取数据标识
        if (!$markID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        $testMarkObj = $this->getModel('TestMark');
        if($this->ifSubject && $this->mySubject){
            $testMarkData =  $testMarkObj->selectData(
                'SubjectID',
                'MarkID in (' . $markID . ')');
            foreach($testMarkData as $i => $iTestMarkData){
                if(!in_array($iTestMarkData['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507');//您不能删除非所属学科自定义打分！
                }
            }
        }
        if ($testMarkObj->deleteData('MarkID in('.$markID.')') === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName, '删除打分规则MarkID为【' . $markID . '】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 数据缓存；
     * @author demo
     */
    public function updateCache(){
        $testmark = $this->getModel('TestMark');
        $testmark->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }

    /**
     * 分离试题打分属性；
     * @author demo
     */
    public function resetMarkAttr(){
        header('Content-type:text/html;charset=utf8');

        $page=$_GET['page'];
        $style=$_GET['style'];
        if(empty($page) || !is_numeric($page)) $page=1;
        $perpage=1000;

        $max=0;
        if(empty($style)){
            $testAttr = $this->getModel('TestAttr');
            $max=$testAttr->selectCount('1=1','TestID');
            $model='TestAttr';
        }

        if(empty($style) && $max<=($page-1)*$perpage){
            $page=1;
            $style=1;
        }

        if($style){
            $testAttr = $this->getModel('TestAttrReal');
            $max=$testAttr->selectCount('1=1','TestID');
            $model='TestAttrReal';
        }


        if($max<=($page-1)*$perpage){
            //写入日志
            $this->adminLog($this->moduleName,'更新试题打分属性');
            exit('更新完毕');
        }

        $where='1=1';
        $limit=($page-1)*$perpage.','.$perpage;

        echo '当前更新'.$model.'第'.$page.'页数据'.($page-1)*$perpage.'-'.$perpage.'<br/>';

        $testAttrMark = $this->getModel('TestAttrMark');
        $testAttrMark->resetMarkAttr($model,$where,$limit);

        //跳转
        exit('<script>location.href="'.U('Test/TestMark/resetMarkAttr',array('page'=>($page+1),'style'=>$style)).'";</script>');
    }
}