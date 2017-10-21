<?php
/**
 * @date 2015年8月29日
 * @author demo
 */
/**
 * 手机扫描二维码 HTML5上传 服务类
 */
namespace Tool\QrCode;
class QrCodeImageUploadModel
{
    /**
     * 描述：生成
     * @param $username
     * @param $no
     * @throws Exception
     * @author demo
     */
    public function qrCode($username,$no){
        import('Common.Tool.QrCode.QrCode');
        $qrCode = new \Tool\QrCode\QrCode();

        //$url=U('Custom/CustomTestStore/mobile','',false,true).'?n='.$no.'&u='.$username;
        $url=C('WLN_HTTP').'/Custom/CustomTestStore/mobile?n='.$no.'&u='.$username;
        $qrCode->setText($url)
            ->setSize(200)
            ->render();
        header("Content-type: image/png;");
    }
    /**
     * 生成制定url的图片
     * @param string $url 路径
     * @param int $size 大小
     * @author demo
     */
    public function qrCodeByUrl($url,$size=200){
        import('Common.Tool.QrCode.QrCode');
        $qrCode = new \Tool\QrCode\QrCode();
        $qrCode->setText($url)
            ->setSize($size)
            ->render();
        header("Content-type: image/png;");
    }

    /**
     * 描述：如果找到图片，返回true，否则超时后返回false
     * @param string $filename 本次上传的文件名
     * @return bool
     * @author demo
     */
    public function webImagePoll($filename){
        session('[pause]');
        $i=0;
        while (true){
            usleep(500000);//0.5秒
            $i++;
            //若得到数据则马上返回数据给客服端，并结束本次请求
            $subMenu = date('Y',time()).'/'.date('md',time());
            $pathFileName = realpath('./Uploads/customTest').DIRECTORY_SEPARATOR.$filename;
            //$fileUrl = '/Uploads/customTest/'.$subMenu.'/'.$filename;
            $fileUrl = '/Uploads/customTest/'.$filename;
            if(file_exists($pathFileName.'.png')){
                return $fileUrl.'.png';
            }
            if(file_exists($pathFileName.'.jpeg')){
                return $fileUrl.'.jpeg';
            }
            if(file_exists($pathFileName.'.gif')){
                return $fileUrl.'.gif';
            }
            //服务器($_POST['time']*0.5)秒后告诉客服端无数据
            if($i>=20){//不能大于60，系统级的超时是30s 15s超时
                return false;
            }
        }
    }

    public function mobile(){

    }


}