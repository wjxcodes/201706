<?php
/**
 * @author demo  
 * @date 2014年11月11日 updateTime 2015-4-2
 */
/**
 * 校本题库优化操作记录模型类，用于查看试题优化操作记录相关操作
 */
namespace Custom\Manage;
class CustomTestTaskLogManage extends BaseController  {
    var $moduleName = '校本题库优化记录列表';
    /**
     * 校本题库试题操作日志列表查看(分学科，查看)
     * @author demo
     */
     public function index(){
         $pageName='试题审核日志操作列表';
         $where=$this->getWhere($_REQUEST);
         $perPage = C('WLN_PERPAGE'); //每页 页数
         /*校本题库审核日志统计总数*/
         $model = $this->getModel('CustomTestTaskLog');
         $count=$model->customTestTaskLogSelectCount($where['data']);
         $this->pageList($count, $perPage, $where['map']);
         $page=page($count,$_GET['p'],$perPage).','.$perPage;
         /*校本题库审核日志分页浏览*/
         $list=$model->customTestTaskLogSelectByPage($where['data'],$page);
         $subject = $this->getApiCommon('Subject/subject');
         /*处理记录显示内容*/
         foreach($list as $i=>$iList){
             $list[$i]['SubjectName']=$subject[$subject[$list[$i]['SubjectID']]['PID']]['SubjectName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
             if($iList['Admin']==''){
                $list[$i]['Admin']='系统';
             }
         }
         $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
         /*载入模板标签*/
         $this->assign('list', $list); // 赋值数据集
         $this->assign('subjectArray', $subjectArray);
         $this->assign('pageName', $pageName);
         $this->display();
     }

    /**
     * 校本题库试题操作日志记录导出
     * @author demo
     */
    public function export() {
        $where=$this->getWhere($_REQUEST);
        $model = $this->getModel('CustomTestTaskLog');
        $count=$model->customTestTaskLogSelectCount($where['data']);// 查询满足要求的总记录
        $perpage = 2000;
        if($count>2000 && empty($_REQUEST['p'])){
            $count=$model->customTestTaskLogSelectCount($where['data']); // 查询满足要求的总记录
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $page=R('Common/SystemLayer/exportPageList',array($count,$perpage,$where['map']));
            $result['data']=$page;
            $result['ifPage']=1;
            $this->setBack($result);
        }
        if(!$_GET['p']){
            $this->setBack('');
        }
        if($_GET['p']=='all'){
            $_GET['p']=0;
        }
        $page=(page($count,$_GET['p'],$perpage)). ',' . $perpage;
        //写入日志
        $this->adminLog($this->moduleName,'导出日志记录where【'.$where['data'].'】');
        $subjectArray=$this->getApiCommon('Subject/subject');
        /*校本题库按条件查询审核日志*/
        $list=$this->getModel('CustomTestTaskLog')->customTestTaskLogSelectByPage($where['data'],$page);
        foreach($list as $i=>$iList){
            $iList['SubjectID']=$subjectArray[$iList['SubjectID']]['ParentName'].$subjectArray[$iList['SubjectID']]['SubjectName'];
            $iList['AddTime']= date('Y-m-d H:i:s', $iList['AddTime']);
            unset($iList['Status']);
            unset($iList['TypesID']);
            unset($iList['SpecialID']);
            unset($iList['GradeID']);
            unset($iList['Diff']);
            unset($iList['Mark']);
            unset($iList['DfStyle']);
            unset($iList['IfChoose']);
            unset($iList['TestNum']);
            unset($iList['TestStyle']);
            unset($iList['OptionWidth']);
            unset($iList['OptionNum']);
            unset($iList['LastUpdateTime']);
            $list[$i]=array_values($iList);
        }
        $keyName=array('日志编号','试题ID','用户名','操作用户','操作描述','操作时间','分值','学科','状态描述');
        $keyWidth=array('10','10','10','10','50','20','10','20','20');
        $excelName=array('title'=>'校本题库操作记录日志列表','excelName'=>'校本题库操作记录日志信息导出Excel');
        $excelMsg=R('Common/SystemLayer/resetExcelMsg',array($keyName,$keyWidth));
        R('Common/SystemLayer/excelExport',array($excelMsg,$list,$excelName));
    }
    /**
     * 日志删除
     * @author demo
     */
    public function delete() {
        $logID = $_POST['id']; //获取数据标识
        if (!$logID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        /*验证记录是否是在7天之内的数据*/
        $logArray = $this->getModel('CustomTestTaskLog')->selectData(
            '*',
            'LogID in (' . $logID . ') and LoadDate between ' . (time() - 7 * 24 * 3600) . ' and ' . time() . '');
        if ($logArray) {
            $this->setError('30816'); //删除失败，请不要删除最近7天内的日志！
            exit;
        }
        /*执行删除或失败记录日志*/
        if ($this->getModel('CustomTestTaskLog')->deleteData(
                'LogID in ('.$logID.')') === false) {
            $this->setError('30302'); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除校本题库审核日记LogID:'.$logID.'');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 获取查询条件
     * @param $where array
     * @return array
     * @author demo
     */
    private function getWhere($where){
        $data=' 1=1 '; //初始化条件
        $map=array(); //分页条件

        //浏览谁的试题.区分学科
        if($this->ifSubject && $this->mySubject){
            $data .= 'and b.SubjectID in ('.$this->mySubject.') ';
        }
        if ($_REQUEST['name'] || $_REQUEST['name']==='0') {
            //简单查询
            if(is_numeric($_REQUEST['name'])){
                $map['name'] = $_REQUEST['name'];
                $data .= ' and a.TestID='.$_REQUEST['name'];
            }else{
                $this->setError('30502');
            }
        } else {
            //高级查询
            if ($_REQUEST['TestID']) {
                if(is_numeric($_REQUEST['TestID'])){
                    $map['TestID'] = $_REQUEST['TestID'];
                    $data .= ' and a.TestID='.$_REQUEST['TestID'];
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' and a.UserName like "%'.$_REQUEST['UserName'].'%"';
            }
            $start = $_REQUEST['Start'];
            $end = $_REQUEST['End'];
            if ($start) {
                if(!checkString('isDate',$_REQUEST['Start'])){
                    $start = date('Y-m-d', $start);
                }
                if(!checkString('isDate',$_REQUEST['End'])){
                    $end = date('Y-m-d', $end);
                }
                if (empty ($end))
                    $end = date('Y-m-d', time());
                if (!checkString('isDate',$start) || !checkString('isDate',$end)) {
                    $this->setError('30719'); //日期格式不正确
                }
                $map['Start'] = strtotime($start);
                $map['End'] = strtotime($end);
                $data .= ' AND a.AddTime between ' . strtotime($start) . ' and ' . strtotime($end) . ' ';
            }
            if ($_REQUEST['SubjectID']) {
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' and b.SubjectID='.$_REQUEST['SubjectID'];
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' and b.Status='.$_REQUEST['Status'];
            }
        }
        $where['data']=$data;
        $where['map']=$map;
        return $where;
    }
}