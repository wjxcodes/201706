<?php

/**
 * @author demo
 * @date 2015年08月17日
 */
class ProcessImage
{
    /**
     * path or files
     * @var array
     */
    public $pf = [];

    /**
     * 描述：批量处理函数
     * 使用方法：http://192.168.253.175:8001/Aat-LocalTest-test?start=2015-07-31&p=1&f=5
     * @import('@.tool.ProcessImage');
     * (new ProcessImage())->index($start,$p,$f);
     * @throws string 参数检查
     * @param string $start 开始日期Y-m-d，形如2015-09-01
     * @param int $p 日期的累加
     * @param int $f 某一天内文件的累加
     * @author demo
     */
    public function index($start,$p=1,$f=1){
        echo '<meta charset="utf-8">';
        if(!strtotime($start)||!is_numeric($p)||$p<1||!is_numeric($f)||$f<1){
            throw new Exception('参数错误，检查start p f');
        }
        $time = strtotime($start)+($p-1)*3600*24;
        $year = date('Y',$time);
        $monthDay = date('md',$time);
        $dayPath = realpath('./Uploads/word/'.$year.'/'.$monthDay);//每天的文件数釿
        echo '<strong>'.($dayPath?('处理目录:'.$dayPath):$year.$monthDay.'目录不存在，处理完成？').'</strong><br>';
        $this->getPathOrFiles($dayPath,$type='path',false);
        $dayWordPaths =$this->pf;
        unset($this->pf);
        $wordPathsCount = count($dayWordPaths);
        if($f<=$wordPathsCount){
            $this->pathWatermark($dayWordPaths[$f-1]);
            $f++;
        }else{
            $p++;
            $f=1;
        }
        redirect(__ACTION__.'?start='.$start.'&p='.$p.'&f='.$f,3);
    }

    /**
     * 描述：接口:图片的父级路径，即文档.files的路徿
     * @throws string
     * @param string $imagePath 例如F:\svn\Dev\www\Uploads\word\2015\0731\xxx文档.files
     * @author demo
     */
    public function pathWatermark($imagePath){
        $this->getPathOrFiles($imagePath,$type='files',false);
        $imageFilePaths = $this->pf;
        unset($this->pf);
        foreach($imageFilePaths as $imageFilePath){
            $separator = DIRECTORY_SEPARATOR=='/'?'/':'\\'.'\\';
            $pattern = '/^(.*)'.$separator.'(image[0-9]*\.)(\w+)$/i';
            preg_match($pattern,$imageFilePath,$matches);
            $suffix = $matches[3];//png
            $filename = $matches[2];//image001.
            $filePath = $matches[1];//F:\svn\Dev\www\Uploads\word\2015\0731\1
            $lowerSuffix = strtolower($suffix);
            if ($lowerSuffix == 'png' || $lowerSuffix == 'jpg' || $lowerSuffix == 'jpeg' || $lowerSuffix == 'gif') {
                $targetPath = $filePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . $filename . $suffix;
                if (!file_exists($filePath . DIRECTORY_SEPARATOR . 'web')) {
                    if(mkdir($filePath . DIRECTORY_SEPARATOR . 'web', 0777, true)==false){
                        throw new Exception('mkdir error');
                    }
                }
                $this->imageWatermark($imageFilePath, $targetPath);
                echo '处理了'.(DIRECTORY_SEPARATOR=='\\'?mb_convert_encoding($imageFilePath,'UTF-8','GBK'):$imageFilePath).'<br>';
            }
        }
    }

    private function imageWatermark($sourcePath,$targetPath){
        import('@.Tool.Watermark');
        $watermark = new Watermark();
        if($watermark->setPrams(
            $type = 'image',
            $srcArr = ['imgSourceSrc'=>$sourcePath,'imgTargetSrc'=>$targetPath],
            $watermarkArr = ['imgWatermarkSrc'=>realpath('./Uploads/word/watermark.png')],
            $position = -1,
            $margin = 20,
            $conditionSizes = ['width'=>80,'height'=>80]
            )){
            if(!$watermark->apply()){
                $this->writeLog($msg=implode('|',$watermark->getError())."\t".$sourcePath,$type='生成错误');
            }
        }else{
            $this->writeLog($msg=implode('|',$watermark->getError())."\t".$sourcePath,$type='参数错误');
            throw new Exception('参数设置错误，请检查错误日志的记录');
        }
    }

    /**
     * 描述：获取路径下承有文件path+fileName
     * @param string $path 路径地址
     * @param string $type path|files
     * @param bool $ifRecursive 是否递归查询
     * @author demo
     */
    private function getPathOrFiles($path,$type='path',$ifRecursive=false){
        foreach (scandir($path) as $iFile) {
            if ($iFile == '.' || $iFile == '..') {
                continue;
            }
            $file = $path . DIRECTORY_SEPARATOR . $iFile;
            if (is_dir($file)) {
                if($ifRecursive){
                    $this->getPathOrFiles($file,$type,$ifRecursive);
                }
                if($type == 'path'){
                    $this->pf[] = $file;
                }
            } else {
                if($type == 'files'){
                    $this->pf[] = $file;
                }
            }
        }

    }

    function writeLog($msg,$type) {
        $record = date('Y-m-d H:m:s') . ' >>> ' . number_format(microtime(TRUE), 5, '.', '') . ' ' . ' : ' . $type . "\t" . $msg;
        $base = 'F:\svn\Dev\www\Uploads\word';
        $logDir = $base . DIRECTORY_SEPARATOR . date('YmdH', time()) . '-log.php';
        if (!file_exists($logDir)) {
            @mkdir($base, 0777, TRUE);
            @file_put_contents($logDir, "<?php die('Access Defined!');?>\r\n", FILE_APPEND);
        }
        if (file_exists($logDir)) {
            @file_put_contents($logDir, $record . "\r\n", FILE_APPEND);
        }
    }


}