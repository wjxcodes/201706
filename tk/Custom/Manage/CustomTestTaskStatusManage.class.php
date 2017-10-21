<?php
/**
 * @author demo  
 * @date 2014年11月11日 updateTime 2015-4-2
 */
/**
 * 校本题库优化试题状态记录模型类，用于查看试题优化状态记录相关操作
 */
namespace Custom\Manage;
class CustomTestTaskStatusManage extends BaseController  {
    var $moduleName = '校本题库试题状态列表';
    /**
     * 校本题库试题状态列表查看(分学科，查看)
     * @author demo
     */
     public function index(){
         $pageName='试题状态列表';
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
                 $data .= ' and a.Status='.$_REQUEST['Status'];
             }
         }
         $perPage = C('WLN_PERPAGE'); //每页 页数
         /*分页查询获取总数*/
         $model = $this->getModel('CustomTestTaskStatus');
         $count=$model->customTestTaskStatusSelectCount($data);
         $this->pageList($count, $perPage, $map);
         $page=page($count,$_GET['p'],$perPage).','.$perPage;
         /*分页查询试题状态*/
         $list=$model->customTestTaskStatusSelectByPage($data,$page);
         $testStatus=$model->getCustomtestStatus();
         $subject = $this->getApiCommon('Subject/subject');
         /*循环处理数据状态信息*/
         foreach($list as $i=>$iList){
             $list[$i]['SubjectName']=$subject[$list[$i]['SubjectID']]['ParentName'].$subject[$list[$i]['SubjectID']]['SubjectName'];
             $list[$i]['Status']=$testStatus[$list[$i]['Status']];
             if($list[$i]['IfDel']==0){$list[$i]['IfDel']='不删除';}else{$list[$i]['IfDel']='删除';}
             if($list[$i]['IfLock']==0){$list[$i]['IfLock']='不锁定';}else{$list[$i]['IfLock']='锁定';}
             if($list[$i]['IfIntro']==0){$list[$i]['IfIntro']='不是';}else{$list[$i]['IfIntro']='是';}
         }
         $subjectArray=$this->getApiCommon('Subject/subjectParentID'); //获取学科数据集
         /*载入模板标签*/
         $this->assign('list', $list); // 赋值数据集
         $this->assign('subjectArray', $subjectArray);
         $this->assign('testStatus', $testStatus);
         $this->assign('pageName', $pageName);
         $this->display();
        
     }
}