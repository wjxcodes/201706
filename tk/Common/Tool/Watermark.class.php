<?php
/**
 * @author demo
 * @date 2015年8月15日
 */
/**
 * 水印操作类
 *
 */
class Watermark {

    /**
     * 错误信息
     * @var array
     */
    private $error = [];

    /**
     * 水印类型 image|font
     * @var string
     */
    private $type = '';

    /**
     * 图片资源
     * @var resource
     */
    private $imgSource = null;

    /**
     * 图片路径
     * @var string
     */
    private $imgSourceSrc = '';

    /**
     * 图片大小数据
     * @var array
     */
    private $imgSourceSize = [];

    /**
     * 水印图片路径
     * @var string
     */
    private $imgWatermarkSrc = '';

    /**
     * 水印图片
     * @var resource
     */
    private $imgWatermark = null;

    /**
     * 水印图片大小数据
     * @var array
     */
    private $imgWatermarkSize = [];

    /**
     * 文字水印的文字
     * @var string
     */
    private $fontWatermarkText = '';

    /**
     * 文字水印的颜色
     * @var array
     */
    private $fontWatermarkRGB = [];

    /**
     * 文字水印的大小
     * @var array
     */
    private $fontWatermarkSize = [];

    /**
     * 文字水印的角度
     * @var int
     */
    private $fontWatermarkAngle = 0;

    /**
     * 文字水印的文字大小
     * @var int
     */
    private $fontWatermarkFontSize = 0;

    /**
     * 文字水印的文字字体路径
     * @var int
     */
    private $fontWatermarkFontFamily = '';

    /**
     * 目标图片路径
     * @var string
     */
    private $imgTargetSrc = '';

    /**
     * 水印外边距
     * @var int
     */
    private $margin = 5;

    /**
     * 水印位置，9宫格
     * 0: 中-中
     * 1: 上-左
     * 2: 上-右
     * 3: 下-右
     * 4: 下-左
     * 5: 上-中
     * 6: 中-右
     * 7: 下-中
     * 8: 中-左
     * @var int
     */
    private $watermarkPosition = 0;

    /**
     * 加水印的条件
     * 图片水印如果为空，则默认大于水印图片的都加水印
     * 文字水印不能为空
     * @var array width height
     */
    private $conditionSize = [];

    /**
     * 构建函数中检查GD库
     * @author demo
     */
    public function __construct(){
        if(!function_exists('imagecreatetruecolor')){
            if(!function_exists('imagecreate')){
                throw new Exception('PHP Need GD Extension');
            }
        }
    }

    public function getError(){
        return $this->error;
    }

    /**
     * 描述：
     * @param string $type image|font
     * @param array $imgArr imgSourceSrc|imgTargetSrc
     * @param array $watermarkArr
     * 图片：imgWatermarkSrc 文字：fontWatermarkText|fontWatermarkColor|fontWatermarkFont|fontWatermarkSize|fontWatermarkAngle
     * @param int $position [-1,8]
     * @param int $margin [0,20]
     * @param array $conditionSize width|height
     * @return bool 是否设置成功
     * @author demo
     */
    public function setPrams($type,$imgArr,$watermarkArr,$position=0,$margin=5,$conditionSize=[]){
        $this->type = $type;
        $this->imgSourceSrc = $imgArr['imgSourceSrc'];
        $this->imgTargetSrc = $imgArr['imgTargetSrc'];
        if(!$this->imgSourceSrc||!$this->imgTargetSrc){
            $this->error[] = '需要设置原始图片和目标图片路径';
        }
        $this->conditionSize = $conditionSize;
        if($type=='image'){
            $this->imgWatermarkSrc = $watermarkArr['imgWatermarkSrc'];
            if(!$this->imgWatermarkSrc){
                $this->error[] = '图片模式需要设置水印图片';
            }
        } else if ($type == 'font') {
            $this->fontWatermarkText = $watermarkArr['fontWatermarkText'];
            $this->fontWatermarkFontFamily = $watermarkArr['fontWatermarkFontFamily'];
            $this->fontWatermarkFontSize = $watermarkArr['fontWatermarkFontSize'];
            $this->fontWatermarkAngle = $watermarkArr['fontWatermarkAngle'];
            $this->fontWatermarkRGB = $this->getFontWatermarkRGB($watermarkArr['fontWatermarkColor']);
            if(!$this->fontWatermarkText||!$this->fontWatermarkFontFamily||!$this->fontWatermarkFontSize||!$this->fontWatermarkRGB){
                $this->error[] = '文字水印必须设置文本、字体、字号、颜色';
            }
        } else {
            $this->error[] = '水印类型type错误';
        }
        if($position==-1){
            $this->watermarkPosition =  rand(0,8);
        }elseif(preg_match('/^[0-8]$/',$position)){
            $this->watermarkPosition =  $position;
        }else{
            $this->error[] = '位置信息错误';
        }
        $this->margin = $margin;
        //如果有错
        return $this->error!=[]?false:true;
    }

    /**
     * 描述：计算文字水印大小
     * @return array width|height
     * @author demo
     */
    private function getFontWatermarkSizes(){
        //计算水印文本的大小
        $box = imagettfbbox(
            $this->fontWatermarkFontSize,
            $this->fontWatermarkAngle,
            $this->fontWatermarkFontFamily,
            $this->fontWatermarkText);
        return ['width'=>$box[2]-$box[6],'height'=>$box[3]-$box[7]];
    }

    /**
     * 描述：获取rgb颜色值
     * @param string $textColor 形如#ffffff
     * @return array|bool
     * @author demo
     */
    private function getFontWatermarkRGB($textColor){
        if (strlen($textColor) == 7 && preg_match('/^#[0-9a-z]/', $textColor)) {
            $r = hexdec(substr($textColor, 1, 2));
            $g = hexdec(substr($textColor, 3, 2));
            $b = hexdec(substr($textColor, 5));
            return ['r' => $r, 'g' => $g, 'b' => $b];
        } else {
            $this->error[] = 'RGB颜色值错误';
            return false;
        }
    }

    /**
     * 不同图片调用方法的接口
     * @param string $name 图片名
     * @param string $action |open|save|
     * @return string 函数名
     * @author demo
     */
    private function getFunction($name, $action = 'open') {
        $name = strtolower($name);
        if (preg_match('/^(.*)\.(jpeg|jpg)$/', $name)) {
            return $action == 'open' ? 'imagecreatefromjpeg' : 'imagejpeg';
        } elseif (preg_match('/^(.*)\.(png)$/', $name)) {
            return $action == 'open' ? 'imagecreatefrompng' : 'imagepng';
        } elseif (preg_match('/^(.*)\.(gif)$/', $name)) {
            return $action == 'open' ? 'imagecreatefromgif' : 'imagegif';
        } else {
            $this->error[] = '图片格式错误';
        }
    }

    /**
     * 获取图片宽高
     * @param resource $img Image Object resource
     * @return array 宽高
     * @author demo
     */
    private function getImgSizes($img){
        return ['width' => imagesx($img), 'height' => imagesy($img)];
    }

    /**
     * 计算水印位置
     * @return array x|y
     * @author demo
     */
    private function getPositions(){
        $imgSource = $this->getImgSizes($this->imgSource);
        if($this->type == 'image'){
            $watermarkSize = $this->getImgSizes($this->imgWatermark);
        }else{
            $watermarkSize = $this->getFontWatermarkSizes();
        }
        $positionX = 0;
        $positionY = 0;
        $margin = $this->margin;
        # Centered
        if($this->watermarkPosition == 0){
            $positionX = ( $imgSource['width'] / 2 ) - ( $watermarkSize['width'] / 2 );
            $positionY = ( $imgSource['height'] / 2 ) - ( $watermarkSize['height'] / 2 );
        }
        # Top Left
        if($this->watermarkPosition == 1){
            $positionX = 0+$margin;
            $positionY = 0+$margin;
        }
        # Top Right
        if($this->watermarkPosition == 2){
            $positionX = $imgSource['width'] - $watermarkSize['width'] - $margin;
            $positionY = 0 + $margin;
        }
        # Footer Right
        if($this->watermarkPosition == 3){
            $positionX = $imgSource['width'] - $watermarkSize['width'] - $margin;
            $positionY = $imgSource['height'] - $watermarkSize['height'] - $margin;
        }
        # Footer left
        if($this->watermarkPosition == 4){
            $positionX = 0 + $margin;
            $positionY = $imgSource['height'] - $watermarkSize['height'] - $margin;
        }
        # Top Centered
        if($this->watermarkPosition == 5){
            $positionX = ( $imgSource['width'] / 2 ) - ( $watermarkSize['width'] / 2 );
            $positionY = 0 + $margin;
        }
        # Center Right
        if($this->watermarkPosition == 6){
            $positionX = $imgSource['width'] - $watermarkSize['width'] - $margin;
            $positionY = ( $imgSource['height'] / 2 ) - ( $watermarkSize['height'] / 2 );
        }
        # Footer Centered
        if($this->watermarkPosition == 7){
            $positionX = ( $imgSource['width'] / 2 ) - ( $watermarkSize['width'] / 2 );
            $positionY = $imgSource['height'] - $watermarkSize['height'] - $margin;
        }
        # Center Left
        if($this->watermarkPosition == 8){
            $positionX = 0 + $margin;
            $positionY = ( $imgSource['height'] / 2 ) - ( $watermarkSize['height'] / 2 );
        }
        return ['x' => $positionX, 'y' => $positionY];
    }

    /**
     * 描述：判断是否加水印
     * @author demo
     */
    private function applyCondition(){
        if($this->conditionSize==[]){
            $width = $this->type=='image'?$this->imgWatermarkSize['width']:$this->fontWatermarkSize['width'];
            $height = $this->type=='image'?$this->imgWatermarkSize['height']:$this->fontWatermarkSize['height'];
            $widthIf = $this->imgSourceSize['width']>($width+$this->margin);
            $heightIf = $this->imgSourceSize['height']>($height+$this->margin);
        }else{
            $widthIf = $this->imgSourceSize['width']>$this->conditionSize['width'];
            $heightIf = $this->imgSourceSize['height']>$this->conditionSize['height'];
        }
        return ($heightIf&&$widthIf)?true:false;
    }

    /**
     * 叠加水印图片
     * @todo 如果需要一张图片叠加多个水印，修改本文件
     * @author demo
     */
    public function apply(){
        //打开图片
        $functionSource = $this->getFunction($this->imgSourceSrc, 'open');
        $this->imgSource = $functionSource($this->imgSourceSrc);
        $this->imgSourceSize = $this->getImgSizes($this->imgSource);
        if(!$this->imgSourceSize['width']){
            $this->error[] = '原始图片读取错误';
            return false;
        }
        if($this->type == 'image'){
            //打开水印
            $functionWatermark = $this->getFunction($this->imgWatermarkSrc, 'open');
            $this->imgWatermark = $functionWatermark($this->imgWatermarkSrc);
            //水印大小
            $this->imgWatermarkSize = $this->getImgSizes($this->imgWatermark);
            if(!$this->imgWatermarkSize['width']){
                $this->error[] = '水印图片读取错误';
                return false;
            }
        }
        if($this->applyCondition()){//小图片不加水印
            //计算水印位置
            $positions = $this->getPositions();
            //增加水印
            if($this->type == 'image'){
                if(imagecopy(
                    $this->imgSource,
                    $this->imgWatermark,
                    $positions['x'], $positions['y'],
                    0, 0,
                    $this->imgWatermarkSize['width'], $this->imgWatermarkSize['height']
                )==false){
                    $this->error[] = 'imagecopy错误';
                    return false;
                }
            }elseif($this->type == 'font'){
                if(imagettftext($this->imgSource,
                    $this->fontWatermarkFontSize,
                    $this->fontWatermarkAngle,
                    $positions['x'], $positions['y'],
                    imagecolorallocate($this->imgSource, $this->fontWatermarkRGB['r'], $this->fontWatermarkRGB['g'], $this->fontWatermarkRGB['b']),
                    $this->fontWatermarkFontFamily,
                    $this->fontWatermarkText)==false){
                    $this->error[] = 'imagettftext错误';
                    return false;
                }
            }
            //保存目标图片
            $functionTarget = $this->getFunction($this->imgTargetSrc, 'save');
            if($functionTarget=='imagejpeg'){
                if($functionTarget($this->imgSource, $this->imgTargetSrc, 100)===false){
                    $this->error[] = $functionTarget.'生成图片错误';
                    return false;
                }
            }else{
                if($functionTarget($this->imgSource, $this->imgTargetSrc)===false){
                    $this->error[] = $functionTarget.'生成图片错误';
                    return false;
                }
            }
            //释放资源
            imagedestroy($this->imgSource);
            if($this->type == 'image'){
                imagedestroy($this->imgWatermark);
            }
        }
        return true;
    }
}
