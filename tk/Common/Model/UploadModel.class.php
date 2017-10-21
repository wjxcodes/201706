<?php
/**
 * @author demo
 * @date 2015年8月25日
 */
/**
 * 上传类，用于上传相关操作
 */
namespace Common\Model;
class UploadModel extends BaseModel{

    public $isOneUpload = false; //是否为单个文件上传，默认为false

    /**
     * 单个文件上传
     * @param string $dir 存放上传文件夹的目录
     * @param string $fileName $_FILES中有效的key
     * @param int $maxSize 上传大小上限
     * @param array $allowExts 允许上传的拓展名
     * @author demo 16-4-21
     */
    private function uploadOne($dir, $maxSize, $allowExts, $fileName='photo'){
        $upload = useToolFunction('UploadFile'); // 实例化上传类
        if(empty($maxSize))
            $maxSize=11457280;
        $upload->maxSize  =$maxSize; // 设置附件上传大小
        $upload->allowExts=$allowExts; // 设置附件上传类型
        $dir=$dir.'/'.date('Y/md',time());
        $realpath         =realpath('./').$dir;
        if(!file_exists($realpath)){
            $this->createPath($realpath);
        }
        $upload->savePath=$realpath.'/'; // 设置附件上传目录
        $info = $upload->uploadOne($_FILES[$fileName]);
        if($info === false){ // 上传错误提示错误信息
            return $upload->getErrorMsg();
        }else{ // 上传成功 获取上传文件信息
            return $dir.'/'.$info[0]['savename'];
        }
    }
    /**
     * 通用上传方法
     * @return string
     * @author demo
     */
    private function upload1($path,$maxSize,$allowExts){
        $upload=useToolFunction('UploadFile'); // 实例化上传类

        if(empty($maxSize))
        $maxSize=553145728;
        $upload->maxSize  =$maxSize; // 设置附件上传大小
        $upload->allowExts=$allowExts; // 设置附件上传类型
        $path=$path.'/'.date('Y/md',time());
		// $url=C('WLN_DOC_HOST_PATH');
        // $realpath=$url.$path;
		// $url=dirname(dirname($_SERVER['DOCUMENT_ROOT'])).'/doc';
		// $realPath = $url.$path;
		// dump($path);
		// dump($url);
		// echo 11111;
		// dump($realpath);die;
		
        if(!file_exists($realpath)){
            $this->createPath($realpath);		
        }
		
		
        $upload->savePath=$realpath.'/'; // 设置附件上传目录
		
        if(!$upload->upload()){ // 上传错误提示错误信息
            return $upload->getErrorMsg();
			
        }
        else{ // 上传成功 获取上传文件信息
            $info=$upload->getUploadFileInfo();
			dump($info);die;
            return $path.'/'.$info[0]['savename'];
        }
    }
	/**
     * 通用上传方法
     * @return string
     * @author demo
		会上传到www目录下
     */
    private function upload($path,$maxSize,$allowExts){
        $upload=useToolFunction('UploadFile'); // 实例化上传类

        if(empty($maxSize))
        $maxSize=553145728;
        $upload->maxSize  =$maxSize; // 设置附件上传大小
        $upload->allowExts=$allowExts; // 设置附件上传类型
        $path=$path.'/'.date('Y/md',time());
        $realpath=realpath('./').$path;
		
        if(!file_exists($realpath)){
            $this->createPath($realpath);
        }
		
        $upload->savePath=$realpath.'/'; // 设置附件上传目录
		
        if(!$upload->upload()){ // 上传错误提示错误信息
            return $upload->getErrorMsg();
        }
        else{ // 上传成功 获取上传文件信息
            $info=$upload->getUploadFileInfo();

            return $path.'/'.$info[0]['savename'];
        }
    }

    /**
     * 递归创建路径
     * @param string $path 路径
     * @return Null
     * @author demo
     */
    private function createPath($path){
        mkdir($path,0755,true);
    }

    /**
     * 对上传方法扩展 用于word和excel上传
     * @param int $maxSize 限制大小
     * @param string $path 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadWordAndExcel($dir='work',$maxSize=51205120){
        $allowExts=array(
            'doc',
            'docx',
            'xls',
            'xlsx'
        ); // 设置附件上传类型
        return $this->upload('/Uploads/'.$dir,$maxSize,$allowExts);
    }

    /**
     * 对上传方法扩展 用于word
     * @param int $maxSize 限制大小
     * @param string $path 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    private function uploadWordOnly($dir='word',$maxSize=51205120){
        $allowExts=array(
            'doc',
            'docx'
			
        ); // 设置附件上传类型
        if($this->isOneUpload){
            return $this->uploadOne('/Uploads/'.$dir, $maxSize, $allowExts);
        }
        return $this->upload('/Uploads/'.$dir,$maxSize,$allowExts);
    }
    /**
     * 对上传方法扩展 用于pdf
     * @param int $maxSize 限制大小
     * @param string $path 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadPdf($dir='bbs',$uptype='bbs',$maxSize=1548576){
        $allowExts=array(
            'pdf',
            'mp3'
        ); // 设置附件上传类型
        $urlPath=$this->uploadOne('/Uploads/'.$dir, $maxSize, $allowExts);
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='pdf文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);

            return array('30725',$urlPath); //输出%s
        }
        if(C('WLN_DOC_HOST')){
            $urlPath = $this->upFileToServer($realPath, $uptype, $dir);

            if (strstr($urlPath, 'error')){
                unlink($realPath);
                $data['description'] = '服务器连接失败！';
                $data['msg']=$urlPath;
                $this->addErrorLog($data);

                return array('30725',$urlPath); //连接服务器失败！
            }
            unlink($realPath); //删除本地文件
        }
        return $urlPath;
    }

    

    /**
     * 对上传方法扩展 用于图片
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadImgOnly($dir='bbs',$maxSize=2048000){
        $allowExts=array(
            'jpg',
            'gif',
            'png',
            'jpeg'
        ); // 设置附件上传类型

        return $this->upload('/Uploads/'.$dir,$maxSize,$allowExts);
    }

    /**
     * 多图片上传
     * @param  int $type 1全成功或者全失败
     * @return array 上传信息数组
     * @author demo
     */
    public function uploadImgArr($dir='teacherAuthData',$maxSize=2097152,$allowExts=array('jpg','gif','png','jpeg'),$type=1){
        $path = '/Uploads/'. $dir ;
        $upload=useToolFunction('UploadFile'); // 实例化上传类
        $upload->maxSize  =$maxSize; // 设置附件上传大小
        $upload->allowExts=$allowExts; // 设置附件上传类型
        $path=$path.'/'.date('Y/md',time());
        $realpath         =realpath('./').$path;

        if(!file_exists($realpath)){
            $this->createPath($realpath);
        }
        $upload->savePath=$realpath.'/'; // 设置附件上传目录

        //检测是否为图片类型
        foreach ($_FILES as $Fkey=> $Fval){
            if(is_array($Fval['name'])) {//同名多文件上传
                $keys       =   array_keys($Fval);
                foreach ($Fval['name'] as $_key=>$_name){
                    $imgArray[$_key]['key'] = $Fkey;
                    foreach ($keys as $key){
                        $imgArray[$_key][$key] = $Fval[$key][$_key];
                    }
                    $p = pathinfo($imgArray[$_key]['name']);
                    if(!in_array($p['extension'], $allowExts) || false === getimagesize($imgArray[$_key]['tmp_name']))
                        return '非法图像文件';
                }
            }else{
                $imgArray[$Fkey] = $Fval;
                $p = pathinfo($imgArray[$Fkey]['name']);
                if(!in_array($p['extension'], $allowExts) || false === getimagesize($imgArray[$Fkey]['tmp_name']))
                    return '非法图像文件';
            }
        }

        /* 上传 并返回上传结果信息 */
        $upResult = array();
        foreach($imgArray as $i=>$img){
            $tmp = $upload->uploadOne($img);
            if($type==1 && empty($tmp)){//其中一个失败则删除其他已成功上传文件
                //上传失败 删掉其他成功文件
                foreach ($upResult as $rs){
                    unlink(realpath('./') . $rs);
                }
                return $upload->getErrorMsg();
            }else{
                $upResult[$i] = empty($tmp) ? $upload->getErrorMsg() : $path.'/'.$tmp[0]['savename'] ;//只返回路径
            }
        }

        return $upResult;
    }

    /**
     * 上传通用方法
     * @param array $uploadData 上传文件设置数据  例：array(maxSize=>1024*1024,allowTypes=>'docx,doc',sPath=>'/Uploads/docfile/');
     * @param string $uptype 传输文件类型 word mht
     * @param string $style 返回文件类型 doc doc mht
     * @return string
     * @author demo
     */
    public function uploadImg($dir='focus',$uptype='bbs',$maxSize=2048000){
        $urlPath = $this->uploadImgOnly($dir,$maxSize);
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='图片文件上传本地失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);

            return 'error|'.$urlPath;
        }
        if(C('WLN_DOC_HOST')){
            //上传文件到服务器 bbs接口上传到focus目录
            $urlPath = $this->upFileToServer($realPath, $uptype, $dir);
            if (strstr($urlPath, 'error')) {
                $data=array();
                $data['description']='图片文件上传服务器失败';
                $data['msg']=$_FILES;
                $this->addErrorLog($data);

                return $urlPath;
            }
            unlink($realPath); //删除本地图片
        }
        return $urlPath;
    }

    /**
     * 描述：上传apk
     * @param string $dir 上传目录
     * @param string $uploadType 上传类型
     * @param int $maxSize 最大大小
     * @return String 文件路径或者错误信息
     * @author demo
     */
    /*
    public function uploadApk($dir='apk',$uploadType='bbs',$maxSize=10240000){
        $webUrlPath = $this->upload('/Uploads/'.$dir,$maxSize,['apk']);
        $realPath = realpath('./') . $webUrlPath;
        if (!file_exists($realPath)) {
            $this->addErrorLog(['description'=>'图片文件上传本地失败','msg'=>$_FILES]);
            return 'error|'.$webUrlPath;
        }
        //上传文件到服务器 bbs接口上传到apk目录
        $fileUrlPath = $this->upFileToServer($realPath, $uploadType, $dir);
        unlink($realPath); //不管成功失败都删除web服务器文件
        return $fileUrlPath;
    }*/

    /**
     * 用户答题过程中非选择题中的图片上传
     * @param string $group 图片分组
     * @param string $directory 目录
     * @author demo
     */
    public function uploadImgForUEditor($dir='customTest', $uptype='bbs',$maxSize=2048000){
        if(IS_POST){
            $output=$this->uploadImg($dir,$uptype,$maxSize);
            if(strstr($output,'error')) return (json_encode(array('state' => $output)));

            $host=C('WLN_DOC_HOST');
            if($host) $output=$host.$output;
            foreach($_FILES as $iFiles){
                $name=$iFiles['name'];
                $size=$iFiles['size'];
                $type=$iFiles['type'];
            }

            return (json_encode(array(
                "original" => $name,
                "name" => basename($output),
                "url" => $output,
                "size" => $size,
                "type" => $type,
                "title" => basename($output),
                "state" => 'SUCCESS'
            )));
        }
    }

    /**
     * 描述：手机APP上传用户作答试题图片 Android IOS通用
     * @param string $dir 存储位置文件夹
     * @return array
     * @author demo
     */
    public function appUserAnswer($dir) {
        $output = $this->uploadImg($dir, $uploadType = 'bbs', $maxSize = 2048000);
        if (strstr($output, 'error')) {
            return [
                'data' => ['name' => $_FILES[0]['name'], 'size' => $_FILES[0]['size'], 'type' => $_FILES[0]['type']],
                'info' => $output,
                'status' => 0
            ];
        };
        $host = C('WLN_DOC_HOST');
        if ($host) {
            $output = $host . $output;
        }
        return [
            'data' => $output,
            'info' => 'success',
            'status' => 1,
        ];
    }

    /**
     * 答题板提交图片上传服务器
     * @param $configRes array editor配置json文件内容
     * @param string $group 图片分组
     * @param string $directory 目录
     * @author demo
     */
    public function uploadScrawlForUEditor($dir='customTest', $uptype='bbs',$maxSize=2048000){
        $config = array(
            "pathFormat" => "Uploads/".$dir."/scrawl/{time}{rand:6}",
            "maxSize" => $maxSize,
            "allowFiles" => '',
            "oriName" => "scrawl.png"
        );

        $base64Data = $_POST['upfile'];
        $img = base64_decode($base64Data);

        $oriName = $config['oriName'];
        $fileSize = strlen($img);
        $fileType = strtolower(strrchr($oriName, '.'));
        $fullName = $this->getFullName($config,$oriName);
        $rootPath = realpath('./');
        if (substr($fullName, 0, 1) != '/') {
            $fullName = '/' . $fullName;
        }
        $filePath = $rootPath.$fullName;
        $fileName = substr($filePath, strrpos($filePath, '/') + 1);
        //$dirname = dirname($filePath);//未使用
        $stateInfo = '';
        //检查文件大小是否超出限制
        if ($fileSize > ($config["maxSize"])){
            exit(json_encode(array('state' => '上传错误，请联系我们！')));
        }
        $folder=dirname($filePath);
        if(!file_exists($folder)){
            $this->createPath($folder);
        }
        file_put_contents($filePath, $img);
        $info = array(
            "state" => $stateInfo,
            "url" => $fullName,
            "title" => $fileName,
            "original" => $oriName,
            "type" => $fileType,
            "size" => $fileSize
        );
        $host=C('WLN_DOC_HOST');
        if($host){
            //上传文件到服务器
            $fileUrl=$this->upFileToServer($filePath, $uptype, $dir);
            if(strstr($fileUrl,'error')){
                exit(json_encode(array('state' => '上传错误，请联系我们！')));
            }else{
                unlink($filePath);
                exit(json_encode(array(
                    "original" => $info['original'],
                    "title" => $info['title'],
                    "url" => $host.$fileUrl,
                    "size" => $info['size'],
                    "type" => $info['type'],
                    "state" => 'SUCCESS'
                )));
            }
        }else{
            exit(json_encode(array('state' => '上传错误，请联系我们！')));
        }
    }

    /**
     * 重命名文件
     * @param $config array 配置项数组
     * @param @oriName string 图片默认名
     * @return string
     */
    protected function getFullName($config,$oriName){
        //替换日期事件
        $t = time();
        //$d = explode('-', date("Y-y-m-d-H-i-s"));//为使用
        $format = $config["pathFormat"];
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法字符,并替换文件名
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }
        $ext = strtolower(strrchr($oriName, '.'));
        return $format . $ext;
    }

    /**
     * 上传word文件
     * @param string $uptype 类型
     * @param string $dir 目录
     * @param int $maxSize 文件大小
     * @return Null|Array 错误返回错误码数组
     * @author demo
     */
    public function uploadWord($dir='docfile',$uptype='work',$maxSize=1548576){
        //上传word
        $urlPath = $this->uploadWordOnly($dir,$maxSize);

        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {

            $data=array();
            $data['description']='Word文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);

            return array('30725',$urlPath); //输出%s
        }
        if(C('WLN_DOC_HOST')){
            if(C('WLN_DOC_OPEN_CHECK')==1){
                $urlPath = $this->wordCheckAndUpload($realPath,$dir,$uptype);
            }else{ //上传文件到服务器work接口上传到docfile目录
                $urlPath = $this->upFileToServer($realPath, $uptype, $dir);
            }

            if (strstr($urlPath, 'error')){
                unlink($realPath);
                $data['description'] = '服务器连接失败！';
                $data['msg']=$urlPath;
                $this->addErrorLog($data);

                return array('30725',$urlPath); //连接服务器失败！
            }
            unlink($realPath); //删除本地文件
        }
        return $urlPath;
    }

    /**
     * 上传音频文件
     * @param string $dir 上传目录
     * @param string $uptype 类型
     * @param array $extension 拓展名限制
     * @param int $maxSize 上传文件最大限制，以MB为单位
     * @author demo 16-4-21
     */
    public function uploadAudioFile($dir='audio', $uptype='', $extension=array(), $maxSize=5){
        $urlPath = $this->uploadOne('/Uploads/'.$dir,($maxSize*pow(1024,2)), $extension, 'audio');
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='音频文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);
            return array('30725',$urlPath); //输出%s
        }
        if(C('WLN_DOC_HOST')){
            $urlPath = $this->upFileToServer($realPath, $uptype, $dir);
            if (strstr($urlPath, 'error')){
                unlink($realPath);
                $data['description'] = '服务器连接失败！';
                $data['msg']=$urlPath;
                $this->addErrorLog($data);
                return array('30725',$urlPath); //连接服务器失败！
            }
            unlink($realPath); //删除本地文件
        }
        return $urlPath;
    }
    /**
     * 上传视频文件
     * @param string $dir 上传目录
     * @param string $uptype 类型
     * @param array $extension 拓展名限制
     * @param int $maxSize 上传文件最大限制，以MB为单位
     * @author demo
     */
    public function uploadVideoFile($dir='video', $uptype='', $extension=array(), $maxSize=5){
        $urlPath = $this->uploadOne('/Uploads/'.$dir,($maxSize*pow(1024,2)), $extension,'file');
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='视频文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);
            return array('30725',$urlPath); //输出%s
        }
        if(C('WLN_DOC_HOST')){
            $urlPath = $this->upFileToServer($realPath, $uptype, $dir);
            if (strstr($urlPath, 'error')){
                unlink($realPath);
                $data['description'] = '服务器连接失败！';
                $data['msg']=$urlPath;
                $this->addErrorLog($data);
                return array('30725',$urlPath); //连接服务器失败！
            }
            unlink($realPath); //删除本地文件
        }
        return $urlPath;
    }

    /**
     * 下载音频文件
     * @param string $path 服务器文件路径
     * @param string $dir 文件服务器所在路径
     * @param string $showName 显示名称
     * @author demo 16-4-21
     */
     public function downloadAudioFile($path,$dir,$showName){
        $tmpArr=$this->calcParamPath($path);
        $time=$tmpArr[2];
        $fileName=$tmpArr[1];
        $checkStr=$tmpArr[0];
        return "/comPaperDoc.php?checkstr=".$checkStr."&uptype=down&style=".$dir."&f=".$fileName."&o=".rawurlencode($showName)."&t=".$time;
    }

    /**
     * 对上传方法扩展 用于excle
     * @param int $maxSize 限制大小
     * @param string $path 文件路径
     * @return mixed 上传结果
     * @author demo
     */
    public function uploadExcelOnly($dir='excel',$maxSize=1048576){
        $allowExts=array(
            'xls',
            'xlsx'
        ); // 设置附件上传类型

        return $this->upload('/Uploads/'.$dir,$maxSize,$allowExts);
    }
    
    /**
     * 上传Excel文件
     * @param string $uptype 类型
     * @param string $dir 目录
     * @param int $maxSize 文件大小
     * @return Null|Array 错误返回错误码数组
     * @author demo
     */
    public function uploadExcel($dir='user',$uptype='excel_stu',$maxSize=2048000){
        //上传excel
        $urlPath = $this->uploadExcelOnly('excel',$maxSize);
		// // dump($urlPath);die;
		
        // $realPath = realpath('./') . $urlPath;
		// // dump($realPath);die;
		// // $realPath='E:\web\www\www/Uploads/excel/2017/0927/59cb1439639d8.xlsx';
        // // if (!file_exists($realPath)) {
			// // // dump($realPath);die;
            // // $data=array();
            // // $data['description']='Excel文件上传失败';
            // // $data['msg']=$_FILES;
            // // $this->addErrorLog($data);
            // // return array('30725',$urlPath); //输出%s
        // // }
        // if(C('WLN_DOC_HOST')){
			// dump(C('WLN_DOC_HOST'));
            // $urlPath = $this->upFileToServer($realPath, $uptype, $dir);
			// dump($urlPath);die;	
            // if (strstr($urlPath, 'error')){
                // unlink($realPath);
                // $data['description'] = '服务器连接失败！';
                // $data['msg']=$urlPath;
                // $this->addErrorLog($data);
                // return array('30725',$urlPath); //连接服务器失败！
            // }
            // unlink($realPath); //删除本地文件
        // }
		// dump($urlPath);die;
        return $urlPath;
    }

    /**
     * 上传word文件并转换文件
     * @return Null|Array 错误返回错误码数组
     * @author demo
     */
    public function uploadWordAndCheck(){
        //上传word
        $urlPath = $this->uploadWordOnly('word');
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='Word文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);
            return array('30725',$urlPath); //输出%s
        }
        //word转html
        $docPath=$this->wordToHtml($realPath,'word','work');
        if(strstr($docPath,'error') || empty($docPath)){
            $data=array();
            $data['description']='Word文件上传失败';
            $data['msg']=$docPath.'###@###文件地址：'.$realPath;
            $this->addErrorLog($data);
            return array('30714',$docPath); //添加失败！上传word文档出错请重试。错误信息【'.$DocPath.'】'
        }
        if($docPath==$realPath){
            $data['DocPath']=$urlPath;
        }else{
            $data['DocPath']=$docPath;
        }

        //开启检测服务则在提取的时候才转htm
        if(C('WLN_DOC_OPEN_CHECK')==1){
            $data['DocFilePath'] = '';
            $data['DocHtmlPath'] = '';
        }else{
            $strLs = substr($data['DocPath'], 0, strrpos($data['DocPath'], '.'));

            $data['DocFilePath'] = $strLs . '.files';
            $data['DocHtmlPath'] = $strLs . '.htm';
        }
        return array($data['DocPath'],$data['DocHtmlPath'],$data['DocFilePath']);
    }
	/**
     * 上传wordexcel文件并转换文件
     * @return Null|Array 错误返回错误码数组
     * @author demo
     */
    public function uploadExcelAndCheck(){
        //上传word
        $urlPath = $this->uploadExcelOnly('excel');
        $realPath = realpath('./') . $urlPath;
        if (!file_exists($realPath)) {
            $data=array();
            $data['description']='excel文件上传失败';
            $data['msg']=$_FILES;
            $this->addErrorLog($data);
            return array('30725',$urlPath); //输出%s
        }
        //word转html
        $docPath=$this->excelToHtml($realPath,'word','work');
        if(strstr($docPath,'error') || empty($docPath)){
            $data=array();
            $data['description']='excel文件上传失败';
            $data['msg']=$docPath.'###@###文件地址：'.$realPath;
            $this->addErrorLog($data);
            return array('30714',$docPath); //添加失败！上传word文档出错请重试。错误信息【'.$DocPath.'】'
        }
        if($docPath==$realPath){
            $data['DocPath']=$urlPath;
        }else{
            $data['DocPath']=$docPath;
        }

        //开启检测服务则在提取的时候才转htm
        if(C('WLN_DOC_OPEN_CHECK')==1){
            $data['DocFilePath'] = '';
            $data['DocHtmlPath'] = '';
        }else{
            $strLs = substr($data['DocPath'], 0, strrpos($data['DocPath'], '.'));

            $data['DocFilePath'] = $strLs . '.files';
            $data['DocHtmlPath'] = $strLs . '.htm';
        }
        return array($data['DocPath'],$data['DocHtmlPath'],$data['DocFilePath']);
    }
    /**
     * 上传文件到服务器
     * @param string $filePath 绝对路径
     * @param string $uptype 传输文件类型 word mht
     * @param string $style 返回文件类型 doc doc mht
     * @notice 存在PHP版本兼容性问题 5.4正常 5.5兼容 >=5.6出现问题
     * @return String
     * @author demo
     */
    public function upFileToServer($filePath,$uptype,$style,$check=0){
		// dump($filePath);die;
        $url=C('WLN_DOC_HOST_IN');
        $urlCheck=C('WLN_DOC_OPEN_CHECK');
        if($check==1 && $urlCheck==1) $url=C('WLN_DOC_HOST_IN_CHECK');
        $checkStr = md5(C('DOC_HOST_KEY').date("Y.m.d",time()));
        $url = $url."/comPaperDoc.php?checkstr=$checkStr&style=$style&uptype=$uptype";
        // $data=array('Filedata'=>'@'.$filePath);
		// dump($url);die;
        //兼容高版本PHP
        if (class_exists('\CURLFile')) {
        $data = array('Filedata' => new \CURLFile(realpath($filePath)));
        } else {
        $data = array('Filedata' => '@' . realpath($filePath));
        }
        return $this->uploadByCURL($data,$url);
    }
    /**
     * 上传post数据到服务器
     * @param string $str 数据内容
     * @param string $uptype 传输文件类型 word mht
     * @param string $style 返回文件类型 doc doc mht
     * @return String
     * @author demo
     */
    private function upStrToServer($str,$uptype,$style){
        $checkStr = md5(C('DOC_HOST_KEY').date("Y.m.d",time()));
        $url = C('WLN_DOC_HOST_IN')."/comPaperDoc.php?checkstr=$checkStr&style=$style&uptype=$uptype";
        $data=array('str'=>$str);
        return $this->uploadByCURL($data,$url);
    }
    /**
     * 上传数据到远程 判断返回数据 如果出错记录到错误日志
     * @param string $postData 提交数据
     * @param string $postUrl 提交地址
     * @return String
     * @author demo
     */
    private function uploadByCURL($postData,$postUrl){
        $output=CURL($postData,$postUrl);
        if(strstr($output,'error') || strstr($output,'comPaperDoc.php')){
            $data=array();
            $data['description'] = '服务器传输错误';
            $data['msg'] = $output;
            $this->addErrorLog($data);
            $tmp=explode('|',$output);
            $str=$output;
            if(count($tmp)>1) $str=$tmp[1];
            return 'error|传输文件出错'.$str;
        }
        return $output;
    }

    /**
     * 计算远程传输路径的参数
     * @param string $urlPath 文件路径
     * @return array 验证码 文件名 时间
     * @author demo
     */
    private function calcParamPath($urlPath){
        $tmpArr=explode('/',$urlPath);
        $tmpLen=count($tmpArr);
        $time=strtotime($tmpArr[$tmpLen-3].$tmpArr[$tmpLen-2]);
        $filename=$tmpArr[$tmpLen-1];
        $checkStr=md5(C('DOC_HOST_KEY').date("Y.m.d",time()));
        return array($checkStr,$filename,$time);
    }

    /**
     * 获取word服务器传输路径
     * @param string $urlPath 文件服务器上的文件路径
     * @param string $uptype 类型
     * @param string $style 文件夹
     * @param string $outputName 输出文件名称
     * @return string 服务器路径
     * @author demo
     */
    public function getDocServerUrl($urlPath,$uptype,$style,$outputName=''){
        $tmpArr=$this->calcParamPath($urlPath);
        $time=$tmpArr[2];
        $fileName=$tmpArr[1];
        $checkStr=$tmpArr[0];

        return "/comPaperDoc.php?checkstr=".$checkStr."&uptype=".$uptype."&style=".$style."&f=".$fileName."&o=".rawurlencode($outputName)."&t=".$time;
    }

    /**
     * 检测word转html是否成功
     * @param string $realPath 文件本地路径
     * @return 文件转换后路径
     * @author demo
     */
    public function checkWord2Html($realPath) {
        //第一次上传检查转换
        return $this->upFileToServer($realPath,'word','wordTest',1);
    }
	/**
     * 检测excel转html是否成功
     * @param string $realPath 文件本地路径
     * @return 文件转换后路径
     * @author demo
     */
    public function checkexcel2Html($realPath) {
        //第一次上传检查转换
        return $this->upFileToServer($realPath,'excle','wordTest',1);
    }

    /*发送字符串到服务器端转换成mht*/
    public function setWordDocument($str,$type='docx'){
        return $this->upStrToServer($str,'mht',$type);
    }

    /*发送字符串到服务器端转换成mht*/
    public function setPdfDocument($str,$type='pdf'){
        return $this->upStrToServer($str,'pdf',$type);
    }

    /**
     * 远端word转html
     * @param string $realPath 文件路径
     * @return String|文件转换后路径
     * @author demo
     */
    public function wordToHtml($realPath,$dir='word',$uptype='work'){
        //开启检测服务
        if(C('WLN_DOC_OPEN_CHECK')==1){
            $output=$this->wordCheckAndUpload($realPath,$dir,$uptype);
        }else{
            //上传文件且转换htm
            $output=$this->upFileToServer($realPath,'word','word');
            unlink($realPath);
        }
        return $output;
    }
	/**
     * 远端excel转html
     * @param string $realPath 文件路径
     * @return String|文件转换后路径
     * @author demo
     */
    public function uploadexcelToServer($realPath,$dir='xls',$uptype='xls'){
        //上传文件且转换htm
        $output=$this->upFileToServer($realPath,'xls','xls');
        unlink($realPath);
        return $output;
    }
    /**
     * 检测并且上传word
     * @param string $realPath 文件路径
     * @return String|文件转换后路径
     * @author demo
     */
    private function wordCheckAndUpload($realPath,$dir='word',$uptype='work'){
        $output = $this->checkWord2Html($realPath);
        if(strstr($output,'error')){
            return $output;
        }
        //第二次仅上传文件
        $output=$this->upFileToServer($realPath,$uptype,$dir);
        unlink($realPath);
        return $output;
    }
	/**
     * 检测并且上传word
     * @param string $realPath 文件路径
     * @return String|文件转换后路径
     * @author demo
     */
    private function excelCheckAndUpload($realPath,$dir='word',$uptype='work'){
        $output = $this->checkExcel2Html($realPath);
        if(strstr($output,'error')){
            return $output;
        }
        //第二次仅上传文件
        $output=$this->upFileToServer($realPath,$uptype,$dir);
        unlink($realPath);
        return $output;
    }
    /**
     * 检查文件word是否可下载
     * @param string $str 处理的字符串
     * @return string 错误返回'error'
     */
    public function checkCreateWord($str){
        $filePath=$this->upStrToServer($str,'checkTest','docx');
        if($this->checkDownUrl($filePath)){
            $url = C('WLN_DOC_HOST_IN').$this->getDocServerUrl($filePath,'checkTest','','');
            $result=file_get_contents($url);
            if($result!='success'){
                return 'error';
            }
            return $filePath;
        }
        return 'error';
    }

    /**
     * 检查下载路径是否正常
     * @param string $url 路径
     * @author demo
     * */
    public function checkDownUrl($url){
        if(strpos($url,'Uploads')){
            return true;
        }

        $data=array();
        $data['description'] ='文档生成路径有误';
        $data['msg'] = $url;
        $this->addErrorLog($data);
        return false;
    }
}
