<?php
/**
 * Created by PhpStorm.
 * User: demo
 * Date: 14-4-22
 * Time: 上午9:16
 */
namespace AatApi\Controller;
class KnowledgeStudyController extends BaseController
{

    /**
     * 提取视频
     * @author demo
     */
    public function video(){
        $kid=$_REQUEST['kID'];//客户端这里是get请求
        $tid=$_REQUEST['tID'];//客户端这里是get请求
        $IData = $this->getApiAat('Exercise/testVideoHtml', $kid,$tid);
        if($IData[0]==1){
            $video = $IData[1];
            if ($_POST['width']&&$_POST['height']) {
                $screenHeight = $_REQUEST['height'];
                $screenWidth = $_REQUEST['width'];
                $video = str_replace(['height=480', 'width=640'], ['','style="height:'.$screenHeight.'px;width:'.$screenWidth.'px;"'], $video);
            }
            echo $video;
        }else{
            echo $IData[1];
        }
    }

}