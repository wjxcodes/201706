<?php
/**
 * @author demo
 * @date 2015年6月7日
 */

/**
 * 基础控制器类，用于处理上传数据相关操作
 */
namespace Common\Controller;
class UploadLayerController extends CommonController{
    private $upload;
    /**
     * 初始化方法
     * @author demo
     */
    protected function _initialize() {
        $this->upload=$this->getModel('Upload');
    }

    /**
     * 对上传方法扩展 用于img
     * @param string $dir 目录名
     * @param string $uptype 类型
     * @param int $maxSize 限制大小
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadImg($dir='focus',$uptype='bbs',$maxSize=2048000){
        return $this->upload->uploadImg($dir,$uptype,$maxSize);
    }
    /**
     * 对上传方法扩展 用于pdf
     * @param string $dir 目录名
     * @param string $uptype 类型
     * @param int $maxSize 限制大小
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadPdf($dir='bbs',$uptype='bbs',$maxSize=2048000){
        return $this->upload->uploadPdf($dir,$uptype,$maxSize);
    }

    /**
     * 描述：上传安卓app安装包
     * @param string $dir 目录名
     * @param string $uploadType 类型
     * @param int $maxSize 限制10m
     * @return mixed
     * @author demo
     */
    /*
    public function uploadApk($dir='apk',$uploadType='bbs',$maxSize=10240000){
        return $this->upload->uploadApk($dir,$uploadType,$maxSize);
    }*/


    /**
     * 对上传方法扩展 用于img本地上传
     * @param string $dir 目录名
     * @param string $uptype 类型
     * @param int $maxSize 限制大小
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadImgOnly($dir='bbs',$maxSize=2048000){
        return $this->upload->uploadImgOnly($dir,$maxSize);
    }

    /**
     * 上传一个文件
     * @author demo 16-6-3
     */
    public function uploadOne($dir, $maxSize, $allowExts, $fileName='photo'){
        return $this->upload->uploadOne($dir, $maxSize, $allowExts, $fileName);
    }

    /**
     * 对上传方法扩展 用于操作api接口
     * @param string $filePath 路径
     * @param string $uptype 类型
     * @param string $style 文件夹
     * @return mixed 上传结果
     * @author demo
     */
    public function upFileToServer($filePath,$uptype,$style){
        return $this->upload->upFileToServer($filePath,$uptype,$style);
    }

    /**
     * 对上传方法扩展 用于word
     * @param string $dir 目录名
     * @param string $uptype 类型
     * @param int $maxSize 限制大小
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadWord($dir='docfile',$uptype='work',$maxSize=1548576){
        return $this->upload->uploadWord($dir,$uptype,$maxSize);
    }

    /**
     * 对上传方法扩展 用于excel
     * @param string $dir 目录名
     * @param string $uptype 类型
     * @param int $maxSize 限制大小
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadExcel($dir='user',$uptype='excel_stu',$maxSize=2048000){
        return $this->upload->uploadExcel($dir,$uptype,$maxSize);
    }

    /**
     * 对上传方法扩展 用于word上传并检测转换
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadWordAndCheck($isOneUpload=false){
        $this->upload->isOneUpload = $isOneUpload;
        return $this->upload->uploadWordAndCheck();
    }

    /**
     * word转html
     * @param string $realPath 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    public function wordToHtml($realPath){
        return $this->upload->wordToHtml($realPath);
    }

    /**
     * word转html
     * @param string $str 字符串内容
     * @param string $type 文件类型
     * @return mixed 上传结果
     * @author demo
     */
    public function setWordDocument($str,$type='docx'){
        return $this->upload->setWordDocument($str,$type);
    }

    /**
     * pdf数据存档
     * @param string $str 字符串内容
     * @param string $type 文件类型
     * @return mixed 上传结果
     * @author demo
     */
    public function setpdfDocument($str,$type='pdf'){
        return $this->upload->setpdfDocument($str,$type);
    }

    /**
     * word转html
     * @param string $realPath 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    public function getDocServerUrl($urlPath,$uptype,$style,$outputName=''){
        return $this->upload->getDocServerUrl($urlPath,$uptype,$style,$outputName);
    }

    /**
     * 检查文件word是否可下载
     * @param string $str 处理的字符串
     * @return string 错误返回'error'
     */
    public function checkCreateWord($str){
        return $this->upload->checkCreateWord($str);
    }

    /**
     * 用户答题过程中非选择题中的图片上传
     * @param string $group 图片分组
     * @param string $directory 目录
     * @author demo
     */
    public function uploadImgForUEditor($group='customTest', $directory='bbs',$maxSize=2048000){
        return $this->upload->uploadImgForUEditor($group, $directory,$maxSize);
    }

    /**
     * 答题板提交图片上传服务器
     * @param $configRes array editor配置json文件内容
     * @param string $group 图片分组
     * @param string $directory 目录
     * @author demo
     */
    public function uploadScrawlForUEditor($dir='customTest', $uptype='bbs',$maxSize=2048000){
        return $this->upload->uploadScrawlForUEditor($dir, $uptype,$maxSize);
    }

    /**
     * 上传音频文件
     * @author demo 16-4-21
     */
    public function uploadAudioFile($dir='audio', $uptype='audio', $config=array()){
        $config =  $config + array(
            'exts' => array('mp3','wav'),
            'maxSize' => 5
        );
        return $this->upload->uploadAudioFile($dir, $uptype, $config['exts'], $config['maxSize']);
    }
    /**
     * 上传视频文件
     * @author demo 16-4-21
     */
    public function uploadVideoFile($dir='video', $uptype='video', $config=array()){
        $config =  $config + array(
            'exts' => array('mp4'),
            'maxSize' => 500
        );
        return $this->upload->uploadVideoFile($dir, $uptype, $config['exts'], $config['maxSize']);
    }
    /**
     * 下载文件，参数参见Common\Model\UploadModel downloadAudioFile方法
     * @author demo 16-4-21
     */
    public function downloadAudioFile($path,$dir,$showName){
        return $this->upload->downloadAudioFile($path, $dir, $showName);
    }

    /**
     * 描述：手机端统一上传接口
     * @author demo
     */
    public function appUpload(){
        if (IS_POST && $_REQUEST['phone']) {
            $action = $_REQUEST['action'];
            switch ($action) {
                case 'appUserAnswer':
                    $result = $this->upload->appUserAnswer($dir = 'userAnswer');
                    break;
                default:
                    $result = ['data' => null, 'info' => '请求参数错误！', 'status' => 0];
                    break;
            }
            exit(json_encode($result));
        }
    }

    /**
     * ueditor编辑器上传方法
     * @param string $dir 目录
     * @author demo
     */
    public function upload($dir='customTest'){
        $dirArray=array('lore','customTest','userAnswer','bbs','correctTest');
        if(!in_array($dir,$dirArray)){
            $result = json_encode(array(
                'state'=> '参数错误'
            ));
            echo $result;
            exit();
        }
        $action = $_REQUEST['action'];
        switch ($action) {
            case 'config':
                //$config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(CONF_PATH.'Home/ueditor-config.json')), true);
                //$result =  json_encode($config);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $result = $this->upload->uploadImgForUEditor($dir);
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $result = $this->upload->uploadScrawlForUEditor($dir);
                break;
            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
        exit();
    }

    /**
     * 检查下载路径是否正常
     * @param string $url 路径
     * @author demo
     * */
    public function checkDownUrl($url){
        return $this->upload->checkDownUrl($url);
    }
}

