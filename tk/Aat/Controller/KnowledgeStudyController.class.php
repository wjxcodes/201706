<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:16
 */
namespace Aat\Controller;
class KnowledgeStudyController extends BaseController
{
    public function _initialize() {
    }

    /**
     * 提取视频
     * @author demo
     */
    public function video(){
        $kid=$_REQUEST['kID'];
        $tid=$_REQUEST['tID'];
        $IData = $this->getApiAat('Exercise/testVideoHtml', $kid,$tid);
        if($IData[0]==1){
            $video = $IData[1];
            $this->assign('url',$video);
            $this->display('Custom@MicroClass/Play');
//            $result = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
//                '<html xmlns="http://www.w3.org/1999/xhtml" >'.
//                '<head>'.
//                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>'.
//                '</head>'.
//                '<body style="margin:0px;padding:0px;">'.
//                '<div id="video">'.$video.'</div>'.
//                '</body>'.
//                '</html>';
//            echo $result;
        }else{
            echo $IData[1];
        }
    }

}