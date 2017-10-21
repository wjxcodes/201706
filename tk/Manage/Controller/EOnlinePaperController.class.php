<?php
/**
 * @author demo
 * @date 2017年3月20日
 */
 /**
  *联考试卷管理控制器类，用于处理联考试卷管理相关操作
  */
namespace Manage\Controller;
class EOnlinePaperController extends BaseController  {
    private $moduleName = '联考试卷管理';
    /**
     * 联考试卷浏览列表
     * @author demo
     */
    public function index() {
        $pageName = '联考试卷管理';
        $map = array();
        $data = ' 1=1 ';
        if($_REQUEST['name']){
            //简单查询
            $map['name'] = $_REQUEST['name'];
            $data .= ' AND PaperName like "%'.$_REQUEST['name'].'%" ';
        }else{
            if($_REQUEST['PaperType']){
                $map['PaperType'] = $_REQUEST['PaperType'];
                $data .= ' AND PaperType= "'.$_REQUEST['PaperType'].'" ';
            }
            if($_REQUEST['PaperName']){
                $map['PaperName'] = $_REQUEST['PaperName'];
                $data .= ' AND PaperName like "%'.$_REQUEST['PaperName'].'%" ';
            }
            if(is_numeric($_REQUEST['SpecialID'])){
                $map['SpecialID'] = $_REQUEST['SpecialID'];
                $data .= ' AND SpecialID = "'.$_REQUEST['SpecialID'].'" ';
            }
            if(is_numeric($_REQUEST['IfWL'])){
                $map['IfWL'] = $_REQUEST['IfWL'];
                $data .= ' AND IfWL = "'.$_REQUEST['IfWL'].'" ';
            }
            if(is_numeric($_REQUEST['Status'])){
                $map['Status'] = $_REQUEST['Status'];
                $data .= ' AND Status = "'.$_REQUEST['Status'].'" ';
            }
        }
        $perpage = C('WLN_PERPAGE');
        $examPaper=$this->getModel('ExamPaper');
        $count = $examPaper->selectCount(
            $data,
            'PaperID',
            '');// 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perpage);
        $page =$page.','.$perpage;
        $list = $examPaper->pageData(
            '*',
            $data,
            'PaperID DESC',
            $page);

        $this->pageList($count,$perpage,$map);
        if($list){
            $paperTypeArr=array();
            $paperTypeBuffer=array();
            $specialArr=array();
            $specialBuffer=array();

            foreach($list as $i=>$iList){
                $paperTypeArr[]=$iList['PaperType'];
                $specialArr[]=$iList['SpecialID'];
            }

            if(!empty($paperTypeArr)){
                $examType=$this->getModel('ExamType');
                $buffer=$examType->selectData('TypeID,TypeName','TypeID in ('.implode(',',$paperTypeArr).')');
                foreach($buffer as $i=>$iBuffer){
                    $paperTypeBuffer[$iBuffer['TypeID']]=$iBuffer['TypeName'];
                }
            }

            if(!empty($specialArr)){
                $topicPaper=$this->getModel('TopicPaper');
                $buffer=$topicPaper->selectData('TopicPaperID,TopicPaperName','TopicPaperID in ('.implode(',',$specialArr).')');
                foreach($buffer as $i=>$iBuffer){
                    $specialBuffer[$iBuffer['TopicPaperID']]=$iBuffer['TopicPaperName'];
                }
            }

            $wlBuffer=[
                0=>'通用',
                1=>'文科',
                2=>'理科'
            ];
            foreach($list as $i=>$iList){
                $list[$i]['SpecialName']=$specialBuffer[$iList['SpecialID']];
                $list[$i]['TypeName']=$paperTypeBuffer[$iList['PaperType']];
                $list[$i]['AddTime']=date('Y-m-d H:i:s',$iList['AddTime']);
                $list[$i]['StatusName']=$iList['Status']==1 ? '锁定': '正常';
                $list[$i]['IfWL']=$wlBuffer[$iList['IfWL']];
            }
        }

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 添加联考试卷
     * @author demo
     */
    public function add() {
        $pageName = '添加联考试卷';
        $act = 'add'; //模板标识

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑联考试卷
     * @author demo
     */
    public function edit() {
        $paperID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($paperID)) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑联考试卷';
        $act = 'edit'; //模板标识
        $examPaper = $this->getModel('ExamPaper');
        $edit = $examPaper->selectData(
            '*',
            'PaperID='.$paperID,
            '',
            '1');

        $examType=$this->getModel('ExamType');
        $examTypeArray=$examType->selectData('*','1=1','TypeID ASC');

        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('examTypeArray', $examTypeArray);
        $this->assign('pageName', $pageName);
        $this->display('EOnlinePaper/add');
    }
    /**
     * 保存联考试卷
     * @author demo
     */
    public function save() {
        $paperID = $_POST['PaperID']; //获取数据标识
        $act = $_POST['act']; //获取模板标识
        //判断数据标识
        if (empty ($paperID) && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        if (empty ($act)) {
            $this->setError('30223');//模板标识不能为空！
        }

        $data = array ();
        $data['PaperName']=$_POST['PaperName'];
        $data['Status'] = $_POST['Status'];
        $data['PaperType'] = $_POST['PaperType'];
        $data['OrderID'] = $_POST['OrderID'];
        $data['IfWL'] = $_POST['IfWL'];
        $data['SpecialID'] = $_POST['SpecialID'];
        if(empty($data['SpecialID'])) $data['SpecialID']=0;

        if ($_FILES['photo']['size']) { //如果修改图片
            $path = R('Common/UploadLayer/uploadPdf'); //上传图片
            if(strstr($path,'error')){
                $this->setError('30725',0,'',$path);
            }
            if(is_array($path)){
                $this->setError($path[0],0,'',$path[1]);
            }
            $data['Http']=$path;
        }else{
            if($act == 'add')
                $this->setError('添加失败！请添加数据文档。');
        }

        $examPaper = $this->getModel('ExamPaper');
        if ($act == 'add') {
            $data['AddTime'] = time();
            if($examPaper->insertData(
                    $data) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加联考试卷【'.$data['PaperName'].'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else
            if ($act == 'edit') {
                if($examPaper->updateData(
                        $data,
                        'PaperID='.$_POST['PaperID']) === false){
                    $this->setError('30311');//修改失败！
                }else{
                    //写入日志
                    $this->adminLog($this->moduleName,'修改联考试卷PaperID为【'.$_POST['PaperID'].'】的数据');
                    $this->showSuccess('修改成功！', __URL__);
                }
            }
    }
    /**
     * 删除联考试卷
     * @author demo
     */
    public function delete() {
        $paperID = $_POST['id']; //获取数据标识
        if (!$paperID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if ($this->getModel('ExamPaper')->deleteData(
                'PaperID in ('.$paperID.')') === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除联考试卷PaperID为【'.$paperID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
    /**
     * 下载pdf
     * @author demo
     */
    public function down(){
        $paperID=$_GET['paperID'];
        //判断数据标识
        if (empty ($paperID)) {
            $this->setError('30301'); //数据标识不能为空！
        }
        //查询文档数据
        $where='PaperID=' . $paperID;
        $edit = $this->getModel('ExamPaper')->selectData(
            '*',
            $where,
            '',
            1);

        //获取文档浏览和下载路径
        $doc=$this->getModel('ExamPaper');
            if(empty($edit[0]['Http']) || !strstr($edit[0]['Http'],'/Uploads')){
                $this->setError('30706'); //该文件不存在
            }

            $url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($edit[0]['Http'],'down','bbs',$edit[0]['PaperName']));

            $strHtml=file_get_contents($url);
            // Begin writing headers
            header ( "Pragma: public" );
            header ( "Expires: 0" );
            header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
            header ( "Cache-Control: public" );
            header ( "Content-Description: File Transfer" );
            // Use the switch-generated Content-Type
            header ( "Content-Type: application/pdf" );
            // Force the download
            $header = "Content-Disposition: attachment; filename=" . $edit[0]['PaperName'] . ".pdf;";
            header ( $header );
            header ( "Content-Transfer-Encoding: binary" );
            echo $strHtml;
    }
}