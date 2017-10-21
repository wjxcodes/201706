<?php
/**
 * @author demo 
 * @date 2014年9月29日
 * @update 2015年1月21日
 */
/**
 *用户使用调查问卷
 *暂时不完善，没有统计，显示图表功能
 */
namespace Manage\Controller;
class QuestionController extends BaseController {
    var $moduleName = '调查列表';
    /**
     * 浏览问卷调查
     * @author demo
     */
    public function index() {
        $pageName = '调查列表';
        $Question = $this->getModel('Question');
        $map = array();
        $data = ' 1=1 ';
        if ($_REQUEST['name']) {
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND UserName = "' . $_REQUEST['name'] . '" ';
        } else {
            //高级查询
            if ($_REQUEST['UserName']) {
                $map['UserName'] = $_REQUEST['UserName'];
                $data .= ' AND UserName = "' . $_REQUEST['UserName'] . '" ';
            }
            if (is_numeric($_REQUEST['Status'])) {
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status = "' . $_REQUEST['Status'] . '" ';
            }
            $start = $_REQUEST['Start'];
            if(strstr($start,'-')){
                $start = strtotime($start);
            }
            $end = $_REQUEST['End'];
            if(strstr($end,'-')){
                $end = strtotime($end);
            }
            if ($start) {
                if (empty ($end)) $end = time();
                $map['Start'] = $start;
                $map['End'] = $end;
                $_REQUEST['Start'] = date('Y-m-d',$start);
                $_REQUEST['End'] = date('Y-m-d',$end);
                $data .= ' AND AddTime between ' . ($start) . ' and ' . ($end) . ' ';
            }           
        }
        $perpage = C('WLN_PERPAGE');
        $count = $Question->selectCount(
            $data,
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 
        $field = "SurveyID,UserName,AddTime,Status";
        $order = "AddTime desc";
        $page=(isset($_GET['p']) ? $_GET['p'] : 1).','.$perpage;
        $list = $Question->pageData(
            $field,
            $data,
            $order,
            $page);
        $this->pageList($count,$perpage,$map);
        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 修改问卷调查状态
     * @author demo
     */
    public function updateStyle() {
        //当点击查看时，该条反馈信息的状态就应该变成已查看
        $id = $_POST['id'];
        $data['Status'] = 1;
        $where = 'SurveyID = '.$id;
        if($this->getModel('Question')->updateData(
                $data,
                $where) === false){
            $this->setBack('error');
        }else{
            $this->setBack('true');
        }
    }
    /**
     * 获取反馈详细信息
     * @author demo
     */
    public function showQuestByID(){
        $id = $_GET['id'];
        $Question = $this->getModel('Question');
        $output = $Question->selectData(
            '*',
            'SurveyID in ('.$id.')');
        if($output){
            $data['Status'] = 1;
            $Question->updateData(
                $data,
                'SurveyID = '.$id);
        }
        //多选的问题，将其变成数组
        $output[0]['SearchMethods'] = explode(',',$output[0]['SearchMethods']);
        //载入模板标签
        $this->assign('output',$output);
        $this->display('Question/indexinfo');
    }
}