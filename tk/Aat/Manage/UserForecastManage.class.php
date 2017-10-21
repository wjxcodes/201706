<?php

/**
 * @author demo
 * @date
 */
/**
 * 用户估分管理类，用于用户估分记录的相关操作
 */
namespace Aat\Manage;
class UserForecastManage extends BaseController  {
    var $moduleName = '用户估分管理';

    /**
     * 用户估分记录列表浏览
     * @author demo 
     */
    public function index() {
        $pageName = '用户估分管理';
        $userForecastModel = $this->getModel('UserForecast');
        $map=array();
        $data=' 1=1 AND ForecastScore>-1';
        if($this->ifSubject && $this->mySubject){
            $data .= 'AND SubjectID in ('.$this->mySubject.')';
        }
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName ="' . $_REQUEST['name'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && $this->mySubject){
                    if(!in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                        $this->setError('30505'); //您不能搜索非所属学科估分记录！
                    }
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' AND SubjectID = "' . $_REQUEST['SubjectID'] . '" ';
            }
        }
        $perpage=C('WLN_PERPAGE');
        $count = $userForecastModel->selectCount(
                $data,
                'ForecastID'
            ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $list =$userForecastModel->pageData(
            '*',
            $data,
            'ForecastID desc',
            $page);
        $this->pageList($count,$perpage,$map);

        if($list){
//            $subjectBuffer=SS('subject');
            $subjectBuffer = $this->getApiCommon('Subject/subject');
            foreach($list as $i=>$iList){
                $list[$i]['SubjectName']=$subjectBuffer[$list[$i]['SubjectID']]['ParentName'].$subjectBuffer[$list[$i]['SubjectID']]['SubjectName'];

                $bufferT=$userForecastModel->selectData(
                    "*",
                    'ForecastID="'.$iList['ForecastID'].'"');
                if($bufferT){
                    $idlist=array();
                    foreach($bufferT as $j=>$jBuffer){
                        $idlist[]=$jBuffer['TestRecordID'];
                    }
                    $list[$i]['idlist']=implode(',',$idlist);
                }
            }
        }
        //学科
//        $subjectArray=SS('subjectParentId'); //获取学科数据集
        $subjectArray = $this->getApiCommon('Subject/subjectParentID');
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subject_array', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->theme('Wln')->display();
    }

    /**
     * 用户估分记录删除
     * @author demo 
     */
    public function delete() {
        $userForecastModel = $this->getModel('UserForecast');
        $id = $_POST['id']; //获取数据标识
        if (!$id) {
            $this->setError('30301','',__URL__);
            // $this->showerror('数据标识不能为空！', __URL__);
        }
        if($this->ifSubject && $this->mySubject){
            $forecastData = $userForecastModel->selectData(
                'SubjectID',
                'ForecastID in ('.$id.')');
            $subjectIDArr = explode(',',$this->mySubject);
            foreach($forecastData as $i=>$iForecastData){
                if(!in_array($iForecastData['SubjectID'],$subjectIDArr)){
                    $this->setError('30506'); //您不能删除非所属学科估分记录！
                }
            }
        }

        if ($userForecastModel->deleteData(
                'ForecastID in (' . $id . ')') === false) {
            $this->setError('30301','',__URL__,''); //删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除用户估分信息ForecastID为【'.$id.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}