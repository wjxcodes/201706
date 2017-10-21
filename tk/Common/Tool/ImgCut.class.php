<?php
/**
 * 图片裁剪类
 * @author demo
 * @date 2015年10月12日
 */
class ImgCut {
    /**
     * 处理图片
     * @param string $param 图片数据
     * @return bool 是否成功
     * @author demo
     */
    public function imgProcess($param) {
        $picPath=$param['picPath'];
        $width=$param['width'];
        $height=$param['height'];
        $x1=$param['x1'];
        $y1=$param['y1'];
        $error='';
        $targetPic = realpath('./') .$picPath;//目标文件覆盖源文件
        //判断图片真实格式
        $imgType = getimagesize($targetPic)['mime'];
        //判断文件类型
        switch($imgType){
            case 'image/jpeg':
                $image = imagecreatefromjpeg($targetPic);
                break;
            case 'image/png':
                $image = imagecreatefrompng($targetPic);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($targetPic);
                break;
            default :
                $error = '30406';
        }
        $copy = $this->_imageCrop($image, $x1, $y1, $width, $height);//裁剪
        $result=imagejpeg($copy, $targetPic, 80);  //输出新图
        if(!$result){
            $error='30407';
        }
        return $error;
    }

    /**
     * 裁切图片
     * @param  resource $image 图片资源
     * @param int $x 横坐标
     * @param int $y 纵坐标
     * @param int $w 裁切宽度
     * @param int $h 裁切高度
     * @return bool|resource 图片资源
     * @author demo
     */
    private function _imageCrop($image, $x, $y, $w, $h) {
        $tw = imagesx($image);
        $th = imagesy($image);
        if ($x > $tw || $y > $th || $w > $tw || $h > $th) {
            return FALSE;
        }
        $dstW = $w;//头像宽
        $dstH = $h;//头像高
        $temp = imagecreatetruecolor($dstW, $dstH);//创建真彩色图片资源连接
        //imagecopyresampled函数的参数：
        //(目标图象连接资源,源图象连接资源,目标X坐标点,目标Y坐标点,源的X坐标点,源的Y坐标点,目标宽度,目标高度,源图象的宽度,源图象的高度)
        imagecopyresampled($temp, $image, 0, 0, $x, $y, $dstW, $dstH, $w, $h);
        return $temp;
    }
}
