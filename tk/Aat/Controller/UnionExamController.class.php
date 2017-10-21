<?php
/**
 * 联考相关
 */
namespace Aat\Controller;
class UnionExamController extends BaseController
{
    public $typeID=1; //考试类型

    public function _initialize() {
    }
    /**
     * 描述：获取下载列表
     * @return [
     *      0=>[
     *              'PaperID'=>'1',
     *              'PaperName'=>'语文',
     *              'IfWL'=>'文科',
     *          ],
     *      1=>[
     *              'PaperID'=>'2',
     *              'PaperName'=>'语文',
     *              'IfWL'=>'文科',
     *          ]
     * ]
     * @author demo
     */
    public function index() {
        $this->checkRequest();

        $examPaper=$this->getModel('ExamPaper');
        $whereIfWL='';
        if($result[1]['IfWL']>0) $whereIfWL=' and IfWL in (0,'.$result[1]['IfWL'].')';
        $buffer=$examPaper->selectData('*','PaperType='.$this->typeID.' and Status=0 '.$whereIfWL,'OrderID ASC,PaperID ASC');

        if($buffer){

            $examType=$this->getModel('ExamType');
            $typeBuffer=$examType->findData('*','TypeID='.$this->typeID);
            $typeArr=array();
            $typeArr[$typeBuffer['TypeID']]=$typeBuffer['TypeName'];


            foreach($buffer as $i=>$iBuffer){
                $buffer[$i]['IfWL']=$result[1]['IfWL']==1?'文科':'理科';
                if(empty($result[1]['IfWL'])) $buffer[$i]['IfWL']='通用';
                $buffer[$i]['TypeName']=$typeArr[$iBuffer['PaperType']];
            }
        }

        $this->assign('pageName','联考试卷下载');
        $this->assign('list',$buffer);
        $this->display();
    }

    /**
     * 描述：下载试卷
     * @params int $paperID 试卷id
     * @return pdf下载
     * @author demo
     */
    public function downPaper() {
        $paperID=$_REQUEST['paperID'];

//        $result=$this->checkIfHaveBack();
//        if($result[0]==0) $this->setError($result[1], 1);

        $examPaper=$this->getModel('ExamPaper');
        $buffer=$examPaper->findData('*','PaperID='.$paperID.' and Status=0 and PaperType='.$this->typeID);
        if($buffer===false){
            exit('试卷不存在');
        }

        if(empty($buffer['Http']) || !strstr($buffer['Http'],'/Uploads')){
            exit('文件不存在'); //该文件不存在
        }
        //$url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($buffer['Http'],'down','bbs',$buffer['PaperName']));
        $url=C('WLN_DOC_HOST').$buffer['Http'];
        header('Location:'.$url);exit();
        $this->setBack($url);exit();
            $strHtml=file_get_contents($url);
            // Begin writing headers
            header ( "Pragma: public" );
            header ( "Expires: 0" );
            header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
            header ( "Cache-Control: public" );
            header ( "Content-Description: File Transfer" );
            // Use the switch-generated Content-Type
            if(strstr($buffer['Http'],'.mp3')){
                header ( "Content-Type: audio/mp3" );
                $header = "Content-Disposition: attachment; filename=" . $buffer['PaperName'] . ".mp3;";
            }
            if(strstr($buffer['Http'],'.pdf')){
                header ( "Content-Type: application/pdf" );
                // Force the download
                $header = "Content-Disposition: attachment; filename=" . $buffer['PaperName'] . ".pdf;";
            }

            header ( $header );
            header ( "Content-Transfer-Encoding: binary" );
            header ( "Content-Length: " . strlen($strHtml) );

            echo $strHtml;
    }
}
